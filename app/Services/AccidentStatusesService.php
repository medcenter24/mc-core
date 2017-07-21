<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Accident;
use App\AccidentStatus;
use App\Events\AccidentStatusChanged;

class AccidentStatusesService
{
    public function set(Accident $accident, AccidentStatus $status, $comment = '')
    {
        $accident->accident_status_id = $status->id;
        $accident->save();

        event(new AccidentStatusChanged($accident, $comment));
    }
}
