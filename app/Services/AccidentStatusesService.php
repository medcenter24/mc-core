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
    const TYPE_ACCIDENT = 'accident';
    const TYPE_DOCTOR = 'doctor';
    const TYPE_HOSPITAL = 'hospital';

    const STATUS_NEW = 'new';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_REJECT = 'reject';
    const STATUS_CLOSED = 'closed';

    public function set(Accident $accident, AccidentStatus $status, $comment = '')
    {
        $accident->accident_status_id = $status->id;
        $accident->save();
        $accident->refresh();

        event(new AccidentStatusChanged($accident, $comment));
    }
}
