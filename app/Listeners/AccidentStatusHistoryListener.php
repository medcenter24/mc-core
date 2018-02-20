<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Listeners;

use App\AccidentStatusHistory;
use App\Events\AccidentStatusChangedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccidentStatusHistoryListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AccidentStatusChangedEvent  $event
     * @return void
     */
    public function handle(AccidentStatusChangedEvent $event)
    {
        AccidentStatusHistory::create([
            'user_id' => auth()->guest() ? 0 : auth()->user()->id,
            'accident_status_id' => $event->getAccident()->accident_status_id,
            'historyable_id' => $event->getAccident()->id,
            'historyable_type' => get_class($event->getAccident()),
            'commentary' => $event->getCommentary(),
        ]);
    }

}
