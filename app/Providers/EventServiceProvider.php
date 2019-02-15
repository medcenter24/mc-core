<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
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
