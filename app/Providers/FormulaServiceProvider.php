<?php

namespace App\Providers;

use App\Services\Formula\FormulaResultService;
use App\Services\Formula\FormulaService;
use App\Services\Formula\FormulaViewService;
use Illuminate\Support\ServiceProvider;

class FormulaServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FormulaService::class, function($app) {
            return new FormulaService(
                $app->make(FormulaViewService::class),
                $app->make(FormulaResultService::class)
            );
        });
    }

    public function provides()
    {
        return [
            FormulaService::class,
        ];
    }
}
