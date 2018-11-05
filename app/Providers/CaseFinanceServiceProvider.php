<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Providers;

use App\Services\AccidentService;
use App\Services\CaseServices\CaseFinanceService;
use App\Services\FinanceConditionService;
use App\Services\Formula\FormulaResultService;
use App\Services\Formula\FormulaService;
use Illuminate\Support\ServiceProvider;

class CaseFinanceServiceProvider extends ServiceProvider
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
        $this->app->singleton(CaseFinanceService::class, function() {
            return new CaseFinanceService(
                $this->app->make(FormulaService::class),
                $this->app->make(AccidentService::class),
                $this->app->make(FinanceConditionService::class),
                $this->app->make(FormulaResultService::class)
            );
        });
    }
}
