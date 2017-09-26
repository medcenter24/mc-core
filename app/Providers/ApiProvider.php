<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class ApiProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if (Request::instance()->server->has('ONLY_API')) {
            Request::instance()->server->set('REQUEST_URI', '/api'
                . Request::instance()->server->get('REQUEST_URI'));
        }
    }
}
