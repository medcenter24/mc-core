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

namespace App\Services\Messenger;

use Cmgmyr\Messenger\MessengerServiceProvider;

/**
 * Class LocMessengerServiceProvider
 * @package App\Services\Messenger
 */
class LocMessengerServiceProvider extends MessengerServiceProvider
{

    /**
     * Setup the configuration for Messenger.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            $this->configPath(),
            'messenger'
        );
    }

    /**
     * Setup the resource publishing groups for Messenger.
     *
     * @throws \ReflectionException
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->getConfigPath() => config_path('messenger.php'),
            ], 'config');

            $this->publishes([
                $this->getMigrationsPath() => base_path('database/migrations'),
            ], 'migrations');
        }
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function getConfigPath(): string
    {
        return $this->getParentDir() .'../config/config.php';
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function getMigrationsPath(): string
    {
        return $this->getParentDir() .'../migrations';
    }

    /**
     * @var string
     */
    private static $parentDir = '';

    /**
     * @return string
     * @throws \ReflectionException
     */
    private function getParentDir(): string
    {
        if (!self::$parentDir) {
            $reflection = new \ReflectionClass(parent::class);
            self::$parentDir = dirname($reflection->getFileName());
        }
        return self::$parentDir;
    }
}
