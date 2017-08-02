<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Listeners;

use App\AccidentStatusHistory as History;
use App\Events\AccidentStatusChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccidentStatusHistory
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
     * @param  AccidentStatusChanged  $event
     * @return void
     */
    public function handle(AccidentStatusChanged $event)
    {
        History::create([
            'user_id' => auth()->user()->id,
            'accident_status_id' => $event->getAccident()->accident_status_id,
            'historyable_id' => $event->getAccident()->id,
            'historyable_type' => get_class($event->getAccident()),
            'commentary' => $event->getCommentary(),
        ]);
    }

}
