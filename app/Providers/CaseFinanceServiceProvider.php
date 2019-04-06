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

namespace App\Providers;

use App\Services\AccidentService;
use App\Services\CaseServices\Finance\CaseFinanceService;
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
