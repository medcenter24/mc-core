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


use App\Exceptions\InconsistentDataException;
use App\Helpers\FileHelper;
use App\Support\Core\Configurable;

/**
 *
 * @see resources/installer/README.md
 *
 * Class InstallerService
 * @package App\Services
 */
class InstallerService extends Configurable
{
    /**
     * The path to json to load configuration from
     */
    private const OPTION_PRE_CONFIG_PATH = 'pre-config-path';
    /**
     * Logger to show state of the installation process
     */
    private const OPTION_LOGGER = 'logger';
    /**
     * array with configuration
     */
    private const OPTION_CONFIG = 'config';

    /**
     * Prepared configuration
     * @var array
     */
    private $config = [];

    /**
     * Logger or printer which allow us to show state
     * @var
     */
    private $logger;

    /**
     * @throws InconsistentDataException
     */
    public function run(): void
    {
        $this->readConfig();
        $this->checkConfiguration();
        $this->installApp();
    }

    /**
     * @throws InconsistentDataException
     */
    private function readConfig(): void
    {
        if ($this->hasOption(self::OPTION_CONFIG)) {
            $this->config = $this->getOption(self::OPTION_CONFIG);
        } else {
            $this->readConfigFromFile($this->getOption(self::OPTION_PRE_CONFIG_PATH));
        }
    }

    /**
     * @param string $path
     * @return array
     * @throws InconsistentDataException
     */
    private function readConfigFromFile(string $path): array 
    {
        if ($this->hasOption(self::OPTION_PRE_CONFIG_PATH) && FileHelper::isReadableFile($path)) {
            $this->status('Loading from the file "' . $path . '"...');
                $this->config = json_decode(FileHelper::getContent($path), true);
                if (!$this->config) {
                    throw new InconsistentDataException('Incorrect file format: ' . $path);
                }
            $this->status('Configuration loaded');
        }
    }

    /**
     * Show the status or write it to the log
     * @param string $message
     * @param string $type
     */
    private function status(string $message, string $type = 'info'): void
    {
        if ($this->hasOption(self::OPTION_LOGGER)) {
            $this->logger = $this->getOption(self::OPTION_LOGGER);
        }
    }

    /**
     * @throws InconsistentDataException
     */
    private function checkConfiguration(): void
    {
        if (!$this->config || !count($this->config)) {
            throw new InconsistentDataException('Config is not provided');
        }

        $this->checkRequiredPaths();
        $this->checkEnvConfig();
    }

    /**
     * Checks that all system directories provided and could be used
     */
    private function checkRequiredPaths(): void
    {

    }

    /**
     * Checks that provided all required parameters for the .env
     */
    private function checkEnvConfig(): void
    {

    }

    private function installApp(): void
    {}
}
