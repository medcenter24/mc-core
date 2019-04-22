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

namespace medcenter24\mcCore\App\Foundation;


use medcenter24\mcCore\App\Helpers\FileHelper;
use medcenter24\mcCore\App\Services\EnvironmentService;
use Illuminate\Foundation\Application as BaseApplication;

class Application extends BaseApplication
{
    /**
     * Get the path to the cached services.php file.
     *
     * @return string
     */
    public function getCachedServicesPath(): string
    {
        return $_ENV['APP_SERVICES_CACHE'] ?? $this->getBootstrapCachePath('services.php');
    }

    /**
     * Get the path to the cached packages.php file.
     *
     * @return string
     */
    public function getCachedPackagesPath(): string
    {
        return $_ENV['APP_PACKAGES_CACHE'] ?? $this->getBootstrapCachePath('packages.php');
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath(): string
    {
        return $_ENV['APP_CONFIG_CACHE'] ?? $this->getBootstrapCachePath('config.php');
    }

    /**
     * Get the path to the routes cache file.
     *
     * @return string
     */
    public function getCachedRoutesPath(): string
    {
        return $_ENV['APP_ROUTES_CACHE'] ?? $this->getBootstrapCachePath('routes.php');
    }

    protected function getBootstrapCachePath($path = ''): string
    {
        if (!EnvironmentService::isInstalled()) {
            $dir = sys_get_temp_dir();
            FileHelper::createDirRecursive([$dir, 'bootstrap', 'cache']);
            EnvironmentService::isTmp(true);
            EnvironmentService::setTmp($dir);
        } else {
            $dir = $this->storagePath();
        }
        $dir .= '/bootstrap/cache/';
        return $dir . $path;
    }

    public function terminate(): void
    {
        parent::terminate();

        // drop tmp dirs for the cache
        if (EnvironmentService::isTmp()) {
            FileHelper::delete(EnvironmentService::getTmp() . '/bootstrap');
        }
    }
}
