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

namespace medcenter24\mcCore\App\Foundation;

use Illuminate\Foundation\PackageManifest;
use Illuminate\Support\Str;
use medcenter24\mcCore\App\Exceptions\CommonException;
use medcenter24\mcCore\App\Foundation\PackageManifest as Mc24PackageManifest;
use medcenter24\mcCore\App\Helpers\FileHelper;
use medcenter24\mcCore\App\Services\Core\EnvironmentService;
use Illuminate\Foundation\Application as BaseApplication;

class Application extends BaseApplication
{

    /**
     * Create a new Illuminate application instance.
     *
     * @param  string|null  $basePath
     * @return void
     */
    public function __construct($basePath = null)
    {
        try {
            $generisConfPath = dirname(__DIR__, 3) . '/config/generis.conf.php';
            if (array_key_exists('APP_CONFIG_PATH', $_ENV)) {
                $generisConfPath = $_ENV['APP_CONFIG_PATH'];
            }
            $environmentService = EnvironmentService::init($generisConfPath);
            $this->useEnvironmentPath($environmentService->getEnvironmentDir());
            $this->loadEnvironmentFrom($environmentService->getEnvironmentFileName());
            $this->useStoragePath($environmentService->getStoragePath());
        } catch (CommonException $e) {
        }

        parent::__construct($basePath);
    }

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
            EnvironmentService::setTmpState(true);
            $dir = rtrim($dir, '/');
            $dir .= '/bootstrap/cache';
            EnvironmentService::setTmp($dir);
        } else {
            $dir = $this->storagePath();
            $dir = rtrim($dir, '/');
            $dir .= '/bootstrap/cache';
        }
        // without / in the end will be failed manifestPath in the PackageManifest : 57
        return rtrim($dir, '/\\') . '/' . ltrim($path, '/\\');
    }

    public function terminate(): void
    {
        parent::terminate();

        self::deleteTmp();
    }

    /**
     * phpunit tests use this
     */
    public static function deleteTmp(): void
    {
        // drop tmp dirs for the cache
        if (EnvironmentService::getTmp() && !empty(EnvironmentService::getTmp())) {
            FileHelper::delete(EnvironmentService::getTmp() . '/bootstrap');
        }
    }

    /**
     * Get or check the current application environment.
     *
     * @param  string|array  $environments
     * @return string|bool
     */
    public function environment(...$environments)
    {
        if (!isset($this['env'])) {
            return false;
        }

        if (count($environments) > 0) {
            $patterns = is_array($environments[0]) ? $environments[0] : $environments;

            return Str::is($patterns, $this['env']);
        }

        return $this['env'];
    }

    public function make($abstract, array $parameters = [])
    {
        if ($abstract === PackageManifest::class) {
            // $abstract = Mc24PackageManifest::class;
            $t = 'a';
            $b = parent::make($abstract, $parameters);
        }

        return parent::make($abstract, $parameters);
    }
}
