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

use Illuminate\Console\Command;
use medcenter24\mcCore\App\Contract\Installer\InstallerParam;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Models\Installer\Params\System\AutoModeParam;
use medcenter24\mcCore\App\Services\EnvironmentService;
use medcenter24\mcCore\App\Services\Installer\InstallerService;
use medcenter24\mcCore\App\Services\Installer\JsonSeedReaderService;
use medcenter24\mcCore\App\Services\ServiceLocatorTrait;

class SeedInstallerCommand extends Command
{

    use ServiceLocatorTrait;

    private const SEED_JSON_ARGUMENT = 'seedJson';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:seed {'.self::SEED_JSON_ARGUMENT.'? : Full path to the .json with seeded data [by default will be installed local environment]}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install application with a seed file.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $seedParams = $this->readSeedParams();
            $autoMode = new AutoModeParam();
            $autoMode->setValue(true);
            $seedParams[] = $autoMode;
            $this->call('setup:environment', $this->listParams($seedParams));
        } catch (InconsistentDataException $e) {
            $this->error($e->getMessage());
        }
    }

    private function readSeedParams(): array
    {
        $seedFile = (string)$this->argument(self::SEED_JSON_ARGUMENT);
        if (empty($seedFile)) {
            $seedFile = dirname(__DIR__, 3) . '/seed.json';
        }
        return $this->getServiceLocator()->get(JsonSeedReaderService::class)->read($seedFile);
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
}
