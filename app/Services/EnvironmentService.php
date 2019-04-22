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
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Exceptions\NotImplementedException;
use medcenter24\mcCore\App\Services\Installer\Params\ConfigDirParam;
use medcenter24\mcCore\App\Services\Installer\Params\ConfigFilenameParam;
use medcenter24\mcCore\App\Services\Installer\Params\DataDirParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvApiDebugParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvApiNameParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvApiPrefixParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvApiStrictParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvApiSubtypeParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvApiVersionParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvAppDebugParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvAppEnvParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvAppKeyParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvAppLogLevelParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvAppModeParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvAppUrlParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvBroadcastDriverParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvCacheDriverParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvCorsAllowOriginDirectorParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvCorsAllowOriginDoctorParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvCustomerNameParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDbConnectionParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDbDatabaseParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDbHostParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDbPasswordParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDbPortParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDbUserNameParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDebugbarEnabledParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDropboxBackupAppParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDropboxBackupKeyParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDropboxBackupRootParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDropboxBackupSecretParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvDropboxBackupTokenParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvFilenameParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvImageDriverParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvMailDriverParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvMailEncryptionParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvMailHostParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvMailPasswordParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvMailPortParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvMailUsernameParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvPusherAppIdParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvPusherAppKeyParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvPusherAppSecretParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvQueueDriverParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvRedisHostParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvRedisPasswordParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvRedisPortParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvSessionDriverParam;
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
     * @throws InconsistentDataException
     * @throws NotImplementedException
     */
    public static function init(string $configPath): self
    {
        if (self::$instance) {
            throw new InconsistentDataException('Environment already initialized');
        }

        self::$instance = new self($configPath);
        return self::instance();
    }

    public static function isInstalled(): bool
    {
        return self::$instance !== null;
    }

    /**
     * Checks or sets current state of environment
     * @param bool $state
     * @return bool
     */
    public static function isTmp(bool $state = false): bool
    {
        if ($state) {
            self::$isTmpEnv = true;
        }

        return self::$isTmpEnv;
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

    public static function getConfigParams(): array
    {
        return [
            new ConfigDirParam(),
            new ConfigFilenameParam(),
            new EnvFilenameParam(),
            new DataDirParam(),
            new EnvApiDebugParam(),
            new EnvApiNameParam(),
            new EnvApiPrefixParam(),
            new EnvApiStrictParam(),
            new EnvApiSubtypeParam(),
            new EnvApiVersionParam(),
            new EnvAppDebugParam(),
            new EnvAppEnvParam(),
            new EnvAppKeyParam(),
            new EnvAppLogLevelParam(),
            new EnvAppModeParam(),
            new EnvAppUrlParam(),
            new EnvBroadcastDriverParam(),
            new EnvCacheDriverParam(),
            new EnvCorsAllowOriginDirectorParam(),
            new EnvCorsAllowOriginDoctorParam(),
            new EnvCustomerNameParam(),
            new EnvDbConnectionParam(),
            new EnvDbDatabaseParam(),
            new EnvDbHostParam(),
            new EnvDbPasswordParam(),
            new EnvDbPortParam(),
            new EnvDbUserNameParam(),
            new EnvDebugbarEnabledParam(),
            new EnvDropboxBackupAppParam(),
            new EnvDropboxBackupKeyParam(),
            new EnvDropboxBackupRootParam(),
            new EnvDropboxBackupSecretParam(),
            new EnvDropboxBackupTokenParam(),
            new EnvImageDriverParam(),
            new EnvMailDriverParam(),
            new EnvMailEncryptionParam(),
            new EnvMailHostParam(),
            new EnvMailPasswordParam(),
            new EnvMailPortParam(),
            new EnvMailUsernameParam(),
            new EnvPusherAppIdParam(),
            new EnvPusherAppKeyParam(),
            new EnvPusherAppSecretParam(),
            new EnvQueueDriverParam(),
            new EnvRedisHostParam(),
            new EnvRedisPasswordParam(),
            new EnvRedisPortParam(),
            new EnvSessionDriverParam(),
        ];
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

        $this->setUpApplication();
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

    private function setUpApplication(): void
    {
        $envFile = realpath($this->getOption(self::ENV_FILE));
        $dir = dirname($envFile);
        $fileName = str_replace($dir, '', $envFile);
        \app()->useEnvironmentPath($dir);
        \app()->loadEnvironmentFrom($fileName);
        \app()->useStoragePath($this->getOption(self::DATA_DIR));
    }
}
