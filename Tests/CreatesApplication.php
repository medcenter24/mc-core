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

declare(strict_types = 1);

namespace medcenter24\mcCore\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Hash;
use medcenter24\mcCore\App\Services\Core\EnvironmentService;

trait CreatesApplication
{

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        putenv('DB_CONNECTION=sqliteTests');

        if (EnvironmentService::isInstalled()) {
            EnvironmentService::terminate();
        }

        if ( !array_key_exists('APP_CONFIG_PATH', $_ENV) ) {
            $_ENV['APP_CONFIG_PATH'] = __DIR__ . '/settings/default.conf.php';
        }

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        Hash::driver('bcrypt')->setRounds(4);

        return $app;
    }
}
