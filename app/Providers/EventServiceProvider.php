<?php

namespace App\Providers;

use App\Events\AccidentStatusChangedEvent;
use App\Events\DoctorAccidentUpdatedEvent;
use App\Events\HospitalAccidentUpdatedEvent;
use App\Listeners\AccidentStatusHistoryListener;
use App\Listeners\DoctorAccidentDoctorAssignmentListener;
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
            DoctorAccidentDoctorAssignmentListener::class,
            SendTelegramMessageOnDocAssignment::class,
        ],
        HospitalAccidentUpdatedEvent::class => [

        ],
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
