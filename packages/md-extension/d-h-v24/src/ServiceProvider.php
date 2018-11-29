<?php

namespace mdExtension\DHV24;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/d-h-v24.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('d-h-v24.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'd-h-v24'
        );

        $this->app->bind('d-h-v24', function () {
            return new DHV24();
        });
    }
}
