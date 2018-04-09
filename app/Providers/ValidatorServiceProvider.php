<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Providers;

use App\Services\DatePeriodService;
use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['validator']->extend('period', function ($attribute, $value, $parameters)
        {
            $datePeriodService = new DatePeriodService();
            if (!is_string($value) || !$datePeriodService->isPeriod($value)) {
                return false;
            }
            return true;
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
