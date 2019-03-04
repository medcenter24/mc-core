<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Providers;


use App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if (App::environment() !== 'production') {
            App::register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            App::register(\Barryvdh\Debugbar\ServiceProvider::class);
            App::register(\Nwidart\Modules\LaravelModulesServiceProvider::class);
        }
    }
}
