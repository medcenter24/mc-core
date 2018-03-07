<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Providers;

use App\Services\Formula\CaseFinanceService;
use Illuminate\Support\ServiceProvider;

class CaseFinanceProvider extends ServiceProvider
{
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
        $this->app->singleton(CaseFinanceService::class, function() {
            return new CaseFinanceService();
        });
    }
}
