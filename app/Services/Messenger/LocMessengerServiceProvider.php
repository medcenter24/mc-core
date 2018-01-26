<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
