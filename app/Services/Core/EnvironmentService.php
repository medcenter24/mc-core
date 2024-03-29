<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Core;

use medcenter24\mcCore\App\Contract\General\Environment;
use medcenter24\mcCore\App\Exceptions\NotImplementedException;
use medcenter24\mcCore\App\Helpers\FileHelper;
use medcenter24\mcCore\App\Support\Core\Configurable;

/**
 * Core environment configuration
 * Class EnvironmentService
 * @package medcenter24\mcCore\App\Services
 */
class EnvironmentService extends Configurable implements Environment
{
    private static ?EnvironmentService $instance = null;

    /**
     * State of the environment
     * (true - we don't have a configuration for the environment
     *  false - environment was configured and it's stable)
     * @var bool
     */
    private static bool $isTmpEnv = false;

    /**
     * Temporary directory which needs to be deleted after the tmp action
     * @var string
     */
    private static string $tmpDir = '';

    /**
     * That class is very low layer of the project
     * so we need to have opportunity to handle errors by himself
     * @var array
     */
    private static array $errors = [];

    /**
     * @param string $configPath
     * @return EnvironmentService
     * @throws NotImplementedException
     */
    public static function init(string $configPath): self
    {
        self::cleanErrors();
        if (!self::$instance) {
            // it brakes tests but it allows to use artisan without installed application
            // throw new InconsistentDataException('Environment already initialized');
            self::$instance = new self($configPath);
        }

        return self::instance();
    }

    public static function isInstalled(): bool
    {
        return self::$instance !== null;
    }

    /**
     * Checks if there are any errors
     * @return bool
     */
    public static function hasErrors(): bool
    {
        return count(static::$errors) > 0;
    }

    public static function getErrors(): array
    {
        return static::$errors;
    }

    private static function addError(string $err = ''): void
    {
        static::$errors[] = $err;
    }

    private static function cleanErrors(): void
    {
        static::$errors = [];
    }

    /**
     * Checks or sets current state of environment
     * @return bool
     */
    public static function isTmp(): bool
    {
        return self::$isTmpEnv;
    }

    public static function setTmpState($state = false): void
    {
        self::$isTmpEnv = (bool) $state;
    }

    /**
     * @param string $path
     */
    public static function setTmp(string $path = ''): void
    {
        self::$tmpDir = $path;
    }

    /**
     * @return string
     */
    public static function getTmp(): string
    {
        return self::$tmpDir;
    }

    /**
     * @return EnvironmentService
     * @throws NotImplementedException
     */
    public static function instance(): self
    {
        if (!self::$instance) {
            $msg = 'You need to initialise your application before EnvironmentService::init($configPath)';
            self::addError($msg);
            throw new NotImplementedException($msg);
        }
        return self::$instance;
    }

    /**
     * EnvironmentService constructor.
     * @param $configPath
     * @throws NotImplementedException
     */
    private function __construct($configPath)
    {
        $options = $this->readConfig($configPath);
        parent::__construct($options);
    }

    /**
     * @param string $path
     * @return array
     * @throws NotImplementedException
     */
    private function readConfig(string $path): array
    {
        if (file_exists($path) && is_readable($path) && !is_dir($path)) {
            $config = include($path);
        } else {
            // When file is not found that means we don't have an access or file not exists
            // trying to allow to install it from artisan
            $reason = '';
            if (!file_exists($path)) {
                $reason .= 'File not found;';
            }
            if (empty($reason) && !is_readable($path)) {
                $reason .= 'Permission to read;';
            }
            if (is_dir($path)) {
                $reason .= 'File expected, directory provided;';
            }

            $msg = 'Configuration file not found [use setup:seed] or CHECK Access [file: '
                .$path
                .($reason ? '; reason: '.$reason : '').']';
            self::addError($msg);
            throw new NotImplementedException($msg);
            // I can't use it because installation process will be failed
            // die('Access to the file '.$path.' denied, please check this path (or try to install env again with setup:environment command)');
        }
        return $config;
    }

    /**
     * @return string
     * @throws NotImplementedException
     */
    public function getEnvironmentPath(): string
    {
        return FileHelper::getRealPath($this->getOption(self::ENV_FILE));
    }

    /**
     * @return string
     * @throws NotImplementedException
     */
    public function getEnvironmentDir(): string
    {
        return dirname($this->getEnvironmentPath());
    }

    /**
     * @return string
     * @throws NotImplementedException
     */
    public function getEnvironmentFileName(): string
    {
        return ltrim('/', str_replace($this->getEnvironmentDir(), '', $this->getEnvironmentPath()));
    }

    /**
     * @return string
     * @throws NotImplementedException
     */
    public function getStoragePath(): string
    {
        return FileHelper::getRealPath($this->getOption(self::DATA_DIR));
    }

    /**
     * For tests we need to rebuild application
     */
    public static function terminate(): void
    {
        self::cleanErrors();
        self::$isTmpEnv = false;
        self::$instance = null;
        if (!empty(self::$tmpDir)) {
            FileHelper::delete(self::$tmpDir);
        }
    }
}
