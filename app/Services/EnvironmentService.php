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

namespace medcenter24\mcCore\App\Services;


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
    /**
     * @var self
     */
    private static $instance;

    /**
     * State of the environment
     * (true - we don't have a configuration for the environment
     *  false - environment was configured and it's stable)
     * @var bool
     */
    private static $isTmpEnv = false;

    /**
     * Temporary directory which needs to be deleted after the tmp action
     * @var string
     */
    private static $tmpDir = '';

    /**
     * @param string $configPath
     * @return EnvironmentService
     * @throws NotImplementedException
     */
    public static function init(string $configPath): self
    {
        if (!self::$instance) {
            // why do I need this, it brakes tests
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
            throw new NotImplementedException('You need to initialise your application before EnvironmentService::init($configPath)');
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
            throw new NotImplementedException('Configuration file not found [use setup:environment]');
        }
        return $config;
    }

    public function getEnvironmentPath(): string
    {
        return realpath($this->getOption(self::ENV_FILE));
    }

    public function getEnvironmentDir(): string
    {
        return dirname($this->getEnvironmentPath());
    }

    public function getEnvironmentFileName(): string
    {
        return str_replace($this->getEnvironmentDir(), '', $this->getEnvironmentPath());
    }

    public function getStoragePath(): string
    {
        return realpath($this->getOption(self::DATA_DIR));
    }

    /**
     * For tests we need to rebuild application
     */
    public static function terminate(): void
    {
        self::$isTmpEnv = false;
        self::$instance = null;
        if (!empty(self::$tmpDir)) {
            FileHelper::delete(self::$tmpDir);
        }
    }
}
