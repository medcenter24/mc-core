<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Accident;
use App\AccidentStatus;
use App\Events\AccidentStatusChangedEvent;
use App\Exceptions\InconsistentDataException;

class AccidentStatusesService
{
    const TYPE_ACCIDENT = 'accident';
    const TYPE_DOCTOR = 'doctor';
    const TYPE_HOSPITAL = 'hospital';
    const TYPE_ASSISTANT = 'assistant';

    const STATUS_NEW = 'new';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_REJECT = 'reject';
    const STATUS_CLOSED = 'closed';

    const STATUS_HOSPITAL_GUARANTEE = 'hospital_guarantee';
    const STATUS_HOSPITAL_INVOICE = 'hospital_invoice';
    const STATUS_ASSISTANT_INVOICE = 'assistant_invoice';
    const STATUS_ASSISTANT_GUARANTEE = 'assistant_guarantee';

    /**
     * Set new status to accident
     * @param Accident $accident
     * @param AccidentStatus $status
     * @param string $comment
     */
    public function set(Accident $accident, AccidentStatus $status, $comment = '')
    {
        $accident->accident_status_id = $status->id;
        $accident->save();
        $accident->refresh();

        \Log::debug('Set new status to accident', ['status_id' => $status->id, 'accident_id' => $accident->id]);
        event(new AccidentStatusChangedEvent($accident, $comment));
    }

    /**
     * @param array $params
     * @throws InconsistentDataException
     * @return AccidentStatus
     */
    public function firstOrFail(array $params = [])
    {
        if (!count($params)) {
            throw new InconsistentDataException('Parameters should been provided');
        }

        return AccidentStatus::firstOrFail($params);
    }
}
