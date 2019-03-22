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

namespace App\Services;


use App\Contract\General\Environment;
use App\Exceptions\InconsistentDataException;
use App\Exceptions\NotImplementedException;
use App\Services\Installer\Params\ConfigDirParam;
use App\Services\Installer\Params\ConfigFilenameParam;
use App\Services\Installer\Params\DataDirParam;
use App\Services\Installer\Params\EnvApiDebugParam;
use App\Services\Installer\Params\EnvApiNameParam;
use App\Services\Installer\Params\EnvApiPrefixParam;
use App\Services\Installer\Params\EnvApiStrictParam;
use App\Services\Installer\Params\EnvApiSubtypeParam;
use App\Services\Installer\Params\EnvApiVersionParam;
use App\Services\Installer\Params\EnvAppDebugParam;
use App\Services\Installer\Params\EnvAppEnvParam;
use App\Services\Installer\Params\EnvAppKeyParam;
use App\Services\Installer\Params\EnvAppLogLevelParam;
use App\Services\Installer\Params\EnvAppModeParam;
use App\Services\Installer\Params\EnvAppUrlParam;
use App\Services\Installer\Params\EnvBroadcastDriverParam;
use App\Services\Installer\Params\EnvCacheDriverParam;
use App\Services\Installer\Params\EnvCorsAllowOriginDirectorParam;
use App\Services\Installer\Params\EnvCorsAllowOriginDoctorParam;
use App\Services\Installer\Params\EnvCustomerNameParam;
use App\Services\Installer\Params\EnvDbConnectionParam;
use App\Services\Installer\Params\EnvDbDatabaseParam;
use App\Services\Installer\Params\EnvDbHostParam;
use App\Services\Installer\Params\EnvDbPasswordParam;
use App\Services\Installer\Params\EnvDbPortParam;
use App\Services\Installer\Params\EnvDbUserNameParam;
use App\Services\Installer\Params\EnvDebugbarEnabledParam;
use App\Services\Installer\Params\EnvDropboxBackupAppParam;
use App\Services\Installer\Params\EnvDropboxBackupKeyParam;
use App\Services\Installer\Params\EnvDropboxBackupRootParam;
use App\Services\Installer\Params\EnvDropboxBackupSecretParam;
use App\Services\Installer\Params\EnvDropboxBackupTokenParam;
use App\Services\Installer\Params\EnvFilenameParam;
use App\Services\Installer\Params\EnvImageDriverParam;
use App\Services\Installer\Params\EnvMailDriverParam;
use App\Services\Installer\Params\EnvMailEncryptionParam;
use App\Services\Installer\Params\EnvMailHostParam;
use App\Services\Installer\Params\EnvMailPasswordParam;
use App\Services\Installer\Params\EnvMailPortParam;
use App\Services\Installer\Params\EnvMailUsernameParam;
use App\Services\Installer\Params\EnvPusherAppIdParam;
use App\Services\Installer\Params\EnvPusherAppKeyParam;
use App\Services\Installer\Params\EnvPusherAppSecretParam;
use App\Services\Installer\Params\EnvQueueDriverParam;
use App\Services\Installer\Params\EnvRedisHostParam;
use App\Services\Installer\Params\EnvRedisPasswordParam;
use App\Services\Installer\Params\EnvRedisPortParam;
use App\Services\Installer\Params\EnvSessionDriverParam;
use App\Support\Core\Configurable;

/**
 * Core environment configuration
 * Class EnvironmentService
 * @package App\Services
 */
class EnvironmentService extends Configurable implements Environment
{
    /**
     * @var self
     */
    private static $instance;

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
     * @return EnvironmentService
     */
    public static function instance(): self
    {
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
    }

    /**
     * @param string $path
     * @return array
     * @throws NotImplementedException
     */
    public function readConfig(string $path): array
    {
        if (file_exists($path) && is_readable($path)) {
            $config = include($path);
        } else {
            throw new NotImplementedException('Configuration file not found [use setup:environment]');
        }
        return $config;
    }
}
