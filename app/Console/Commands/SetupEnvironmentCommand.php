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

namespace App\Console\Commands;

use App\Exceptions\InconsistentDataException;
use App\Helpers\FileHelper;
use App\Services\EnvironmentService;
use App\Services\Installer\ConfigurableParam;
use App\Services\Installer\InstallerService;
use Illuminate\Console\Command;

class SetupEnvironmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:environment
        {--json-config-path : Pre-configured static .json file}
    ';

    public function __construct()
    {
        // extend signatures to have possibility to set any of environment params
        /** @var ConfigurableParam $param */
        foreach (EnvironmentService::getConfigParams() as $param) {
            $this->signature .= "\n{ --" .$param->getParamName().' : '.$param->question().' }';
        }

        parent::__construct();
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configurator of the application';

    /**
     * @var InstallerService
     */
    private $installerService;

    /**
     * @var string
     */
    private $configFilePath;

    /**
     * @var string
     */
    private $envFileName;

    /**
     * Execute the console command.
     * @param InstallerService $installerService
     * @throws InconsistentDataException
     */
    public function handle(InstallerService $installerService): void
    {
        if (EnvironmentService::isInstalled()) {
            $this->error('Environment already configured');
            return;
        }

        $this->installerService = $installerService;
        $this->initPreconfiguredParams();
        $this->initProgressLogger();

        try {
            $this->installerService->generateConfig();
            if ($this->installerService->requiresAdditionalParams()) {
                $this->requireAdditionalParams();
            }
            if (!$this->installerService->canBeInstalled()) {
                throw new InconsistentDataException('Incorrect configuration for the installer');
            }
            $this->printConfigTable();
            $this->install();
        } catch (InconsistentDataException $e) {
            $this->error($e->getMessage());
        }
    }

    private function printConfigTable(): void
    {
        $headers = ['ID', 'Value'];
        $this->table($headers, $this->installerService->configAsArray());
    }

    /**
     * @throws InconsistentDataException
     */
    private function initPreconfiguredParams(): void
    {
        if ($this->hasOption('json-config-path') && $this->option('json-config-path')) {
            $this->installerService->setOption(InstallerService::OPTION_PRE_CONFIG_PATH, (string) $this->option('json-config-path'));
        } else {
            $config = [];
            /** @var ConfigurableParam $configParam */
            foreach (EnvironmentService::getConfigParams() as $configParam) {
                if ($this->hasOption($configParam->getParamName()) && $this->option($configParam->getParamName())) {
                    $configParam->setValue($this->option($configParam->getParamName()));
                    if (!$configParam->isValid()) {
                        $this->error('Parameter ' . $configParam->getParamName() . ' is not valid');
                    } else {
                        $config[] = $configParam;
                    }
                }
            }
            if (count($config)) {
                $this->installerService->setOption(InstallerService::OPTION_CONFIG, $config);
            }
        }
    }

    private function initProgressLogger(): void
    {
        $self = $this;
        $this->installerService->setOption(InstallerService::OPTION_LOGGER, function ($msg) use($self) {$self->info($msg);});
    }

    /**
     * @throws InconsistentDataException
     */
    private function install(): void
    {
        if ($this->confirm('Are you sure want to install application with these parameters?')) {
            $this->installerService->install();
        }
    }

    /**
     * Ask user about more params for installer
     * @throws InconsistentDataException
     */
    public function requireAdditionalParams(): array
    {
        /** @var ConfigurableParam $requiredParam */
        foreach ($this->installerService->getRequiredParams() as $requiredParam) {
            do {
                $requiredParam->setValue( (string) $this->ask($requiredParam->question(), $requiredParam->defaultValue()) );
            } while(!$requiredParam->isValid());
        }
    }


    private function setUp(): void
    {
        $this->createConfigFile();
        $this->envFileSetUp();

        // folders for the data storing
        $this->createStorage();
    }

    private function createConfigFile(): void
    {
        do {
            $this->configFilePath = (string)$this->ask('Configuration file name [for paths and constants]:', ['generis.conf']);
        } while(
                !FileHelper::isFileAvailable($this->configFilePath)
                || !is_writable($this->configFilePath)
                || !FileHelper::writeConfig($this->configFilePath)
            );
    }

    private function envFileSetUp(): void
    {
        $this->createEnvFile();
        $this->fillEnvFileProperties();
    }

    private function createEnvFile(): void
    {
        do {
            $this->envFileName = (string)$this->ask('New name for the laravel environment file', '.env');
        } while(!FileHelper::isFileAvailable($this->envFileName)
                || !FileHelper::writeFile($this->envFileName, ''));
    }

    private function fillEnvFileProperties(): void
    {
        $env = (string) $this->argument('env');
        switch ($env) {
            case 'production':
                $this->info('Production environment');
                $properties = $this->installerService->getProdEnvProps();
                $properties = array_merge($properties,
                    $this->installerService->getRequiredInteractive($this->installerService->getProdInteractiveKeys()));
                break;
            case 'development':
                $this->info('Development environment');
                $properties = $this->installerService->getDevEnvProps();
                break;
            default: $properties = $this->getInteractiveEnvProps();
        }

        FileHelper::writeFile($this->envFileName, $properties);
        $this->info('Environment configuration saved.');
    }

    private function getInteractiveEnvProps(array $props = []): string
    {

    }

    private function createStorage(): void
    {
        $this->info('todo');
    }
}
