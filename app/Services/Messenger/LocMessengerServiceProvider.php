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
            vendor_path('cmgmyr/messenger/config/config.php'),
            'messenger'
        );
    }

    /**
     * Setup the resource publishing groups for Messenger.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                vendor_path('cmgmyr/messenger/config/config.php') => config_path('messenger.php'),
            ], 'config');

            $this->publishes([
                vendor_path('cmgmyr/messenger/migrations') => base_path('database/migrations'),
            ], 'migrations');
        }
    }
}
