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


use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\FileHelper;
use medcenter24\mcCore\App\Services\EnvironmentService;
use medcenter24\mcCore\App\Services\Installer\Params\ConfigDirParam;
use medcenter24\mcCore\App\Services\Installer\Params\DataDirParam;
use medcenter24\mcCore\App\Services\Installer\Params\EnvParam;
use medcenter24\mcCore\App\Services\Installer\Params\NullParam;
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
     * Prepared configuration
     * @var array
     */
    private $config = [];

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
        $this->config = EnvironmentService::getConfigParams();
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
     * @throws InconsistentDataException
     */
    public function install(): void
    {
        if (!$this->canBeInstalled()) {
            throw new InconsistentDataException('Incorrect configuration for the installer');
        }

        $this->createConfigDir();
        $this->createDataDir();

        $this->writeConfigFile();
        $this->envFileSetup();

        // folders for the data storing
        $this->createStores();
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
     * @throws InconsistentDataException
     */
    private function envFileSetup(): void
    {
        $created = false;
        /** @var ConfigurableParam $param */
        if ($paramConfigDir = $this->getParam(self::PROP_CONFIG_DIR)) {
            $confDir = $paramConfigDir->getValue();
            $created = $paramConfigDir instanceof ConfigDirParam
                && is_writable($confDir)
                && FileHelper::writeFile($confDir . DIRECTORY_SEPARATOR . $this->getParam(self::PROP_ENV_FILE_NAME)->getValue(), $this->getEnvData());
        }

        if (!$created) {
            throw new InconsistentDataException('Config File can not be created');
        }
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

        $this->status('Stores created.');
    }
}
