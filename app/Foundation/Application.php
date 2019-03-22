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

namespace App\Foundation;


class Application extends \Illuminate\Foundation\Application
{
    private function isDeploy()
    {
        return !file_exists(__DIR__.'/../../vendor/autoload.php');
    }

    private function getDeployedBootstrapPath($path = '')
    {
        $bsPath = $this->isDeploy() ? realpath(__DIR__ . '/../../../data/bootstrap/')
            : $this->bootstrapPath();

        return $path ? str_replace($this->bootstrapPath(), $this->getDeployedBootstrapPath(), $path) : $bsPath;
    }

    /**
     * Get the path to the cached services.php file.
     *
     * @return string
     */
    public function getCachedServicesPath()
    {
        return $this->getDeployedBootstrapPath(parent::getCachedServicesPath());
    }

    /**
     * Get the path to the cached packages.php file.
     *
     * @return string
     */
    public function getCachedPackagesPath()
    {
        return $this->getDeployedBootstrapPath(parent::getCachedPackagesPath());
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath()
    {
        return $this->getDeployedBootstrapPath(parent::getCachedConfigPath());
    }

    /**
     * Get the path to the routes cache file.
     *
     * @return string
     */
    public function getCachedRoutesPath()
    {
        return $this->getDeployedBootstrapPath(parent::getCachedRoutesPath());
    }
}
