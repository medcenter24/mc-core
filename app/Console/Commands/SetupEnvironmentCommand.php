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

namespace medcenter24\mcCore\App\Console\Commands;

use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\FileHelper;
use medcenter24\mcCore\App\Services\EnvironmentService;
use medcenter24\mcCore\App\Services\Installer\ConfigurableParam;
use medcenter24\mcCore\App\Services\Installer\InstallerService;
use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;

class SetupEnvironmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:environment';

    public function __construct()
    {
        // extend signatures to have possibility to set any of environment params
        /** @var ConfigurableParam $param */
        foreach (EnvironmentService::getConfigParams() as $param) {
            $this->signature .= "\n{ --" .$param->getParamName().'= : '.$param->question().' }';
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
     * Execute the console command.
     * @param InstallerService $installerService
     * @throws InconsistentDataException
     * @throws \medcenter24\mcCore\App\Exceptions\NotImplementedException
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
        $this->installerService->setOption(InstallerService::OPTION_LOGGER, static function ($msg) use($self) {$self->info($msg);});
    }

    /**
     * @throws InconsistentDataException
     * @throws \medcenter24\mcCore\App\Exceptions\NotImplementedException
     */
    private function install(): void
    {
        if ($this->confirm('Are you sure want to install application with these parameters?')) {
            $this->installerService->install();

            $this->reloadApp();
            $this->migrate();
        } else {
            $this->error('Aborted');
        }
    }

    /**
     * @throws InconsistentDataException
     * @throws \medcenter24\mcCore\App\Exceptions\NotImplementedException
     */
    private function reloadApp(): void
    {
        EnvironmentService::init(
            $_ENV['APP_CONFIG_PATH'] ?? dirname(__DIR__, 4) . '/config/generis.conf.php'
        );
        $dot = Dotenv::create(app()->environmentPath(), app()->environmentFile());
        with($dot)->overload();
        with(new LoadConfiguration())->bootstrap(app());
    }

    private function migrate(): void 
    {
        if ($this->confirm('Do you want to migrate DB?')) {

            if (env('DB_CONNECTION') === 'sqlite') {
                // create new file for the DB
                $file = env('DB_DATABASE');
                $path = mb_substr($file, 0, mb_strrpos($file, DIRECTORY_SEPARATOR));
                $dirs = explode(DIRECTORY_SEPARATOR, $path);
                FileHelper::createDirRecursive($dirs);
                FileHelper::writeFile($file, '');
            }

            $this->call('migrate');
            $this->seed();
        }
    }
    
    private function seed(): void
    {
        if ($this->confirm('Do you want to seed DB?')) {
            $this->call('db:seed');
            $this->addSuperAdmin();
        }
    }
    
    private function addSuperAdmin(): void
    {
        if ($this->confirm('Do you want to create Super Admin?')) {
            $this->call('user:add', ['roles' => 'login,admin']);
        }
    }
}
