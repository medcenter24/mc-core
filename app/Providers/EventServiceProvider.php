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

use App\Events\AccidentPaymentChangedEvent;
use App\Events\AccidentStatusChangedEvent;
use App\Events\DatePeriodChangedEvent;
use App\Events\DoctorAccidentUpdatedEvent;
use App\Events\HospitalAccidentUpdatedEvent;
use App\Listeners\AccidentPaymentListener;
use App\Listeners\AccidentStatusHistoryListener;
use App\Listeners\DatePeriodInterpretationListener;
use App\Listeners\SendTelegramMessageOnDocAssignment;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        AccidentStatusChangedEvent::class => [
            AccidentStatusHistoryListener::class,
        ],
        DoctorAccidentUpdatedEvent::class => [
            SendTelegramMessageOnDocAssignment::class,
        ],
        HospitalAccidentUpdatedEvent::class => [
        ],
        DatePeriodChangedEvent::class => [
            DatePeriodInterpretationListener::class,
        ],
        AccidentPaymentChangedEvent::class => [
            AccidentPaymentListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
