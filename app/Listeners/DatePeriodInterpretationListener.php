<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Listeners;

use App\Events\DatePeriodChangedEvent;
use App\Services\DatePeriod\DatePeriodInterpretationService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DatePeriodInterpretationListener
{

    /**
     * @var DatePeriodInterpretationService
     */
    private $service;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DatePeriodInterpretationService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param DatePeriodChangedEvent $event
     * @param DatePeriodInterpretationService $service
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function handle(DatePeriodChangedEvent $event)
    {
        $datePeriod = $event->getDatePeriod();
        $this->service->update($datePeriod);
    }
}
