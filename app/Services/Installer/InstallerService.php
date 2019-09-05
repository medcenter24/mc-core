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

namespace medcenter24\mcCore\App\Services\Installer;


use medcenter24\mcCore\App\Contract\Installer\EnvParam;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\FileHelper;
use medcenter24\mcCore\App\Models\Installer\Params\ConfigurableParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiDebugParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiNameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiPrefixParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiStrictParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiSubtypeParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiVersionParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppDebugParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppEnvParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppKeyParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppLogLevelParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppModeParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppUrlParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvBroadcastDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvCacheDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvCorsAllowOriginDirectorParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvCorsAllowOriginDoctorParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvCustomerNameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbConnectionParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbDatabaseParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbPasswordParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbPortParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbUserNameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDebugbarEnabledParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDropboxBackupAppParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDropboxBackupKeyParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDropboxBackupRootParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDropboxBackupSecretParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDropboxBackupTokenParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvImageDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvLogChannelParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvLogSlackWebhookUrlParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailEncryptionParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailPasswordParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailPortParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailUsernameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvPusherAppIdParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvPusherAppKeyParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvPusherAppSecretParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvQueueDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvRedisHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvRedisPasswordParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvRedisPortParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvSessionDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\NullParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\AdminEmailParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\AdminNameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\AdminPasswordParam;
use medcenter24\mcCore\App\Models\Installer\Params\System\AutoModeParam;
use medcenter24\mcCore\App\Models\Installer\Params\System\ConfigDirParam;
use medcenter24\mcCore\App\Models\Installer\Params\System\ConfigFilenameParam;
use medcenter24\mcCore\App\Models\Installer\Params\System\DataDirParam;
use medcenter24\mcCore\App\Models\Installer\Params\System\EnvFilenameParam;
use medcenter24\mcCore\App\Services\EnvironmentService;
use medcenter24\mcCore\App\Support\Core\Configurable;

/**
 *
 * @see resources/installer/README.md
 *
 * Class InstallerService
 * @package medcenter24\mcCore\App\Services
 */
class InstallerService extends Configurable
{
    /**
     * The path to json to load configuration from
     */
    public const OPTION_PRE_CONFIG_PATH = 'pre-config-path';
    /**
     * Logger to show state of the installation process
     */
    public const OPTION_LOGGER = 'logger';
    /**
     * array with configuration
     */
    public const OPTION_CONFIG = 'config';

    /**
     * $config['config-dir']
     */
    public const PROP_CONFIG_DIR = 'config-dir';

    /**
     * Config file name
     * generis.conf
     */
    public const PROP_CONFIG_FILE_NAME = 'config-filename';

    /**
     * .env file name
     * .laravel.env
     */
    public const PROP_ENV_FILE_NAME = 'env-filename';

    /**
     * $config['data-dir']
     */
    public const PROP_DATA_DIR = 'data-dir';

    /**
     * Auto mode to setup everything automatically
     */
    public const PROP_AUTO_MODE = 'auto';

    public const PROP_ADMIN_EMAIL = 'admin-email';
    public const PROP_ADMIN_NAME = 'admin-name';
    public const PROP_ADMIN_PASSWORD = 'admin-password';

    /**
     * Prepared configuration
     * @var array
     */
    private $config = [];

    public static function getConfigParams(): array
    {
        return array_merge([
            new AutoModeParam(),
            new AdminPasswordParam(),
            new AdminEmailParam(),
            new AdminNameParam(),
            new ConfigDirParam(),
            new ConfigFilenameParam(),
            new DataDirParam(),
        ], self::getEnvParams());
    }

    /**
     * .env
     * @return array
     */
    public static function getEnvParams(): array
    {
        return [
            new EnvFilenameParam(),
            new EnvLogSlackWebhookUrlParam(),
            new EnvLogChannelParam(),
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
     * @throws InconsistentDataException
     */
    public function generateConfig(): void
    {
        $conf = $this->readConfig();
        $this->fillConfig($conf);
    }

    /**
     * @param array $conf
     */
    private function fillConfig(array $conf = []): void
    {
        $this->config = self::getConfigParams();
        /** @var ConfigurableParam $configParam */
        foreach ($conf as $configParam) {
            /** @var ConfigurableParam $item */
            foreach ($this->config as $key => $item) {
                if ($item->getParamName() === $configParam->getParamName()) {
                    $this->config[$key] = $configParam;
                    break;
                }
            }
        }
    }

    /**
     * Linear array with configurations
     * @return array
     */
    public function configAsArray(): array
    {
        $arr = [];
        /** @var ConfigurableParam $param */
        foreach ($this->config as $param) {
            $arr[] = [$param->getParamName(), $param->getValue()];
        }
        return $arr;
    }

    /**
     * @return string
     * @throws InconsistentDataException
     */
    public function install(): string
    {
        if (!$this->canBeInstalled()) {
            throw new InconsistentDataException('Incorrect configuration for the installer');
        }

        $this->createConfigDir();
        $this->createDataDir();

        $this->writeConfigFile();
        $newEnvFilePath = $this->envFileSetup();

        // folders for the data storing
        $this->createStores();
        return $newEnvFilePath;
    }

    public function canBeInstalled(): bool
    {
        return !EnvironmentService::isInstalled() && $this->isValidConfig();
    }

    private function isValidConfig(): bool
    {
        $valid = true;
        /** @var ConfigurableParam $param */
        foreach ($this->config as $param) {
            if (!$param->isValid() && $param->isRequired()) {
                return false;
            }
        }
        return $valid;
    }

    /**
     * @throws InconsistentDataException
     */
    private function readConfig(): array
    {
        $conf = [];
        if ($this->hasOption(self::OPTION_CONFIG)) {
            $conf = $this->getOption(self::OPTION_CONFIG);
        } elseif($this->hasOption(self::OPTION_PRE_CONFIG_PATH)) {
            $conf = $this->readConfigFromFile($this->getOption(self::OPTION_PRE_CONFIG_PATH));
        }
        return $conf;
    }

    /**
     * @param string $path
     * @throws InconsistentDataException
     * @return array
     */
    private function readConfigFromFile(string $path): array
    {
        $conf = [];
        if ($this->hasOption(self::OPTION_PRE_CONFIG_PATH) && FileHelper::isReadable($path)) {
            $this->status('Loading from the file "' . $path . '"...');
                $conf = json_decode(FileHelper::getContent($path), true);
                if (!$conf) {
                    throw new InconsistentDataException('Incorrect file format: ' . $path);
                }
            $this->status('Configuration loaded');
        }
        return $conf;
    }

    /**
     * Show the status or write it to the log
     * @param string $message
     */
    private function status(string $message): void
    {
        if ($this->hasOption(self::OPTION_LOGGER)) {
            $this->getOption(self::OPTION_LOGGER)($message);
        }
    }

    private function getParam(string $paramKey): ConfigurableParam
    {
        /** @var ConfigurableParam $param */
        foreach ($this->config as $param) {
            if ($param->getParamName() === $paramKey) {
                return $param;
            }
        }
        return new NullParam();
    }

    // !--------  installation

    /**
     * @throws InconsistentDataException
     */
    private function createConfigDir(): void
    {
        $param = $this->getParam(self::PROP_CONFIG_DIR);
        $created = $param && $param instanceof ConfigDirParam
            && $param->isValid()
            && (
                FileHelper::isDirExists($param->getValue())
                    || FileHelper::createDir($param->getValue())
            );

        if (!$created) {
            throw new InconsistentDataException('Configuration directory can not be created');
        }

        $this->status('Configuration directory created.');
    }

    /**
     * @throws InconsistentDataException
     */
    private function createDataDir(): void
    {
        $param = $this->getParam(self::PROP_DATA_DIR);
        $created = $param && $param instanceof DataDirParam
            && $param->isValid()
            && (
                FileHelper::isDirExists($param->getValue())
                || FileHelper::createDir($param->getValue())
            );

        if (!$created) {
            throw new InconsistentDataException('Configuration directory can not be created');
        }

        $this->status('Data directory created.');
    }

    /**
     * @throws InconsistentDataException
     */
    private function writeConfigFile(): void
    {
        $created = false;
        /** @var ConfigurableParam $param */
        if ($paramConfigDir = $this->getParam(self::PROP_CONFIG_DIR)) {
            $confDir = $paramConfigDir->getValue();
            $created = $paramConfigDir instanceof ConfigDirParam
                && $paramConfigDir->isValid()
                && is_writable($confDir)
                && FileHelper::writeConfig($confDir . DIRECTORY_SEPARATOR . $this->getParam(self::PROP_CONFIG_FILE_NAME)->getValue(),
                    [
                        EnvironmentService::ENV_FILE => $confDir . DIRECTORY_SEPARATOR . $this->getParam(self::PROP_ENV_FILE_NAME)->getValue(),
                        EnvironmentService::DATA_DIR => $this->getParam(self::PROP_DATA_DIR)->getValue(),
                    ]);
        }

        if (!$created) {
            throw new InconsistentDataException('Config File can not be created');
        }
    }

    /**
     * @return string
     * @throws InconsistentDataException
     */
    private function envFileSetup(): string
    {
        $created = false;
        /** @var ConfigurableParam $param */
        if ($paramConfigDir = $this->getParam(self::PROP_CONFIG_DIR)) {
            $confDir = $paramConfigDir->getValue();
            $created = $paramConfigDir instanceof ConfigDirParam
                && is_writable($confDir)
                && FileHelper::writeFile($this->getNewEnvFilePath(), $this->getEnvData());
        }

        $waiter = 0;
        while (!FileHelper::isReadable($this->getNewEnvFilePath())) {
            if ($waiter > 10) {
                throw new InconsistentDataException('Env file can not be read more than 10 sec');
            }
            sleep(1);
            $waiter++;
        }

        if (!$created) {
            throw new InconsistentDataException('Config File can not be created');
        }

        return $this->getNewEnvFilePath();
    }

    /**
     * @return string
     */
    private function getNewEnvFilePath(): string
    {
        return $this->getParam(self::PROP_CONFIG_DIR)->getValue()
            . DIRECTORY_SEPARATOR
            . $this->getParam(self::PROP_ENV_FILE_NAME)->getValue();
    }

    private function getEnvData(): string
    {
        $data = '';
        /** @var ConfigurableParam $param */
        foreach ($this->config as $param) {
            if ($param instanceof EnvParam) {
                $data .= $param->getParamName() . '="' . $param->getValue() . "\"\n";
            }
        }
        return $data;
    }

    /**
     * generates needed folders for the environment
     */
    private function createStores(): void
    {
        $foldersMap = [
            'app' => ['public'],
            'debugbar' => [],
            'documents' => [],
            'exports' => [],
            'framework' => ['cache', 'sessions', 'testing', 'views'],
            'imports' => ['cases'],
            'logs' => [],
            'medialibrary' => [],
            'pdfCaseReports' => [],
            'pdfForms' => [],
            'signature' => [],
            'tmp' => [],
            'uploads' => [],
            'bootstrap' => ['cache'],
        ];

        $param = $this->getParam(self::PROP_DATA_DIR);
        $rootDir = $param->getValue();
        foreach ($foldersMap as $key => $val) {
            FileHelper::createDir($rootDir. '/'. $key);
            foreach ($val as $item) {
                FileHelper::createDir($rootDir. '/'. $key .'/' . $item);
            }
        }

        $this->status('Data directories created.');
    }
}
