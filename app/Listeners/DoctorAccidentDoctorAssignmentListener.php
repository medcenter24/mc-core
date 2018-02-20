<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Listeners;

use App\AccidentStatus;
use App\AccidentStatusHistory;
use App\Events\DoctorAccidentUpdatedEvent;
use App\Services\AccidentStatusesService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DoctorAccidentDoctorAssignmentListener
{
    /**
     * @var AccidentStatusesService
     */
    private $accidentStatusesService;

    /**
     * Create the event listener.
     *
     * @param  AccidentStatusesService $accidentStatusesService
     */
    public function __construct(AccidentStatusesService $accidentStatusesService)
    {
        $this->accidentStatusesService = $accidentStatusesService;
    }

    /**
     * Handle the event.
     *
     * @param  DoctorAccidentUpdatedEvent  $event
     * @return void
     */
    public function handle(DoctorAccidentUpdatedEvent $event)
    {
        $prev = $event->getPreviousData();
        $doctorAccident = $event->getDoctorAccident();

        if (
            $doctorAccident->doctor_id &&
            (!$prev || $prev->doctor_id != $doctorAccident->doctor_id)
        ) {

            $accidentStatus = AccidentStatus::firstOrCreate([
                'title' => AccidentStatusesService::STATUS_ASSIGNED,
                'type' => AccidentStatusesService::TYPE_DOCTOR,
            ]);

            $this->accidentStatusesService->set($doctorAccident->accident, $accidentStatus, $event->getCommentary());
        }
    }
}
