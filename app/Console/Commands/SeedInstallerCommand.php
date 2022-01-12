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

namespace medcenter24\mcCore\App\Console\Commands;

use Illuminate\Console\Command;
use medcenter24\mcCore\App\Contract\Installer\InstallerParam;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\Arr;
use medcenter24\mcCore\App\Helpers\FileHelper;
use medcenter24\mcCore\App\Models\Installer\Params\System\AutoModeParam;
use medcenter24\mcCore\App\Services\Core\EnvironmentService;
use medcenter24\mcCore\App\Services\Installer\GuiSettingsService;
use medcenter24\mcCore\App\Services\Installer\InstallerService;
use medcenter24\mcCore\App\Services\Installer\JsonSeedReaderService;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;

/**
 * php artisan setup:seed [--force]
 *
 * Class SeedInstallerCommand
 * @package medcenter24\mcCore\App\Console\Commands
 */
class SeedInstallerCommand extends Command
{
    use ServiceLocatorTrait;

    private const SEED_JSON_ARGUMENT = 'seedJson';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:seed 
        {'.self::SEED_JSON_ARGUMENT.'? : Full path to the .json with seeded data [by default will be installed local environment]}
        {--force= : Required for installation folders will be automatically created and cleaned }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install application with a seed file.';

    private JsonSeedReaderService $jsonSeedReaderService;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->info('Installation run from the "'.$this->getSeedFilePath().'"');
            $this->cleanIfForceReinstall();

            $seedParams = $this->readSeedParams();
            // make auto installation without questions
            $autoMode = new AutoModeParam();
            $autoMode->setValue('true');
            $seedParams[] = $autoMode;

            $this->call('setup:environment', $this->listParams($seedParams));

            $this->saveGuiSettings();
            $this->info('Settings installed');

        } catch (InconsistentDataException $e) {
            $this->error($e->getMessage());
        }
        $this->info('Seeding finished');
    }

    private function cleanIfForceReinstall(): void
    {
        if ($this->hasOption('force')) {
            $data = $this->getJsonSeedReaderService()->getRawData($this->getSeedFilePath());
            if (Arr::keysExists($data, ['configurations', 'global', 'config-path'])
                && Arr::keysExists($data, ['configurations', 'global', 'data-path'])) {

                if (FileHelper::isDirExists($data['configurations']['global']['config-path'])) {
                    FileHelper::delete($data['configurations']['global']['config-path']);
                }
                FileHelper::createDir($data['configurations']['global']['config-path']);

                if (FileHelper::isDirExists($data['configurations']['global']['data-path'])) {
                    FileHelper::delete($data['configurations']['global']['data-path']);
                }
                FileHelper::createDir($data['configurations']['global']['data-path']);

                EnvironmentService::terminate();
            }
        }
    }

    private function getSeedFilePath(): string
    {
        $seedFile = (string)$this->argument(self::SEED_JSON_ARGUMENT);
        if (empty($seedFile)) {
            $seedFile = dirname(__DIR__, 3) . '/seed.json';
        }
        return $seedFile;
    }

    /**
     * @return array
     * @throws InconsistentDataException
     */
    private function readSeedParams(): array
    {
        return $this->getJsonSeedReaderService()->read($this->getSeedFilePath());
    }

    private function getJsonSeedReaderService(): JsonSeedReaderService
    {
        if (!isset($this->jsonSeedReaderService)) {
            $this->jsonSeedReaderService = $this->getServiceLocator()->get(JsonSeedReaderService::class);
        }
        return $this->jsonSeedReaderService;
    }

    /**
     * @param array $params
     * @return array
     * @throws InconsistentDataException
     */
    private function listParams(array $params): array
    {
        $transformed = [];

        /** @var InstallerParam $configParam */
        foreach (InstallerService::getConfigParams() as $configParam) {
            $found = false;
            foreach ($params as $param) {
                if ($param instanceof $configParam) {
                    $transformed['--'.$param->getParamName()] = $param->getValue();
                    $found = true;
                }
            }
            if (!$found) {
                throw new InconsistentDataException('The param "' . $configParam->getParamName() . '" is not found');
            }
        }

        return $transformed;
    }

    /**
     * Store doctors and directors configurations to use it within development only
     * it has sense to use it with local environment only, before compilation
     * @throws InconsistentDataException
     */
    private function saveGuiSettings(): void
    {
        if ($this->getJsonSeedReaderService()->get(EnvironmentService::PROP_APP_ENV)->getValue() === 'local') {

            $params = [];

            foreach ([
                         JsonSeedReaderService::PROP_DIRECTOR_DEV_HOST,
                         JsonSeedReaderService::PROP_DIRECTOR_PROD_HOST,
                         JsonSeedReaderService::PROP_DOCTOR_DEV_HOST,
                         JsonSeedReaderService::PROP_DOCTOR_PROD_HOST,
                         JsonSeedReaderService::PROP_DIRECTOR_DEV_PROJECT_NAME,
                         JsonSeedReaderService::PROP_DIRECTOR_PROD_PROJECT_NAME,
                         JsonSeedReaderService::PROP_DIRECTOR_DOCTOR_DEV_HOST,
                         JsonSeedReaderService::PROP_DIRECTOR_DOCTOR_PROD_HOST,
                    ] as $prop) {
                $params[$prop] = $this->getJsonSeedReaderService()->get($prop)->getValue();
            }

            $this->getServiceLocator()->get(GuiSettingsService::class)
                ->storeConfig($params);
        }
    }
}
