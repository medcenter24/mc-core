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
declare(strict_types=1);

namespace medcenter24\mcCore\App\Providers;

use medcenter24\mcCore\App\Events\Accident\Caseable\AccidentUpdatedEvent;
use medcenter24\mcCore\App\Events\Accident\Caseable\DoctorAccidentUpdatedEvent;
use medcenter24\mcCore\App\Events\Accident\Caseable\HospitalAccidentUpdatedEvent;
use medcenter24\mcCore\App\Events\Accident\Status\AccidentStatusChangedEvent;
use medcenter24\mcCore\App\Events\Accident\Payment\AccidentPaymentChangedEvent;
use medcenter24\mcCore\App\Events\DatePeriodChangedEvent;
use medcenter24\mcCore\App\Events\InvoiceChangedEvent;
use medcenter24\mcCore\App\Listeners\Accident\UpdateAccidentStatus\OnInvoiceUpdated;
use medcenter24\mcCore\App\Listeners\LogPaymentChanges;
use medcenter24\mcCore\App\Listeners\AccidentStatusHistoryListener;
use medcenter24\mcCore\App\Listeners\Accident\UpdateAccidentStatus\OnAccidentUpdateListener;
use medcenter24\mcCore\App\Listeners\DatePeriodInterpretationListener;
use medcenter24\mcCore\App\Listeners\Accident\UpdateAccidentStatus\OnDoctorAccidentUpdated;
use medcenter24\mcCore\App\Listeners\Accident\UpdateAccidentStatus\OnHospitalAccidentUpdated;
use medcenter24\mcCore\App\Listeners\SendTelegramMessageOnDocAssignment;
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
        AccidentUpdatedEvent::class => [
            OnAccidentUpdateListener::class,
        ],
        DoctorAccidentUpdatedEvent::class => [
            OnDoctorAccidentUpdated::class,
            SendTelegramMessageOnDocAssignment::class,
        ],
        HospitalAccidentUpdatedEvent::class => [
            OnHospitalAccidentUpdated::class,
        ],
        DatePeriodChangedEvent::class => [
            DatePeriodInterpretationListener::class,
        ],
        AccidentPaymentChangedEvent::class => [
            LogPaymentChanges::class,
        ],
        InvoiceChangedEvent::class => [
            OnInvoiceUpdated::class,
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
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
