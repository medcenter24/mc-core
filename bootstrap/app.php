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

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new \App\Foundation\Application(
    realpath(__DIR__.'/../')
);

// if it is deployment then file will be above the laravel root
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    $app->useEnvironmentPath(realpath(__DIR__.'/../../config'));
    $app->loadEnvironmentFrom('.laravel.env');

    // replace storage path if provided
    $app->useStoragePath( realpath(__DIR__ . '/../../data/laravel') );

    if (!function_exists('vendor_path')) {
        function vendor_path ($path = '') {
            return realpath(__DIR__ . '/../../vendor') . ($path ? DIRECTORY_SEPARATOR.$path : $path);
        }
    }
}

// vendor path needed for some of the serviceProviders ie MessengerServiceProvider
if (!function_exists('vendor_path')) {
    function vendor_path ($path = '') {
        return realpath(__DIR__ . '/../vendor') . ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
