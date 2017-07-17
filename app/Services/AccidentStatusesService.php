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

    /**
     * 1. new
     * Doctor case
     * 2. assigned
     * 3. in_progress
     * 5. sent
     * 6. paid
     * 7. doctor rejected
     * // Accident
     * 8. accident rejected
     * 9. accident closed
     */
    const ACCIDENT_STATUSES = [
        [
            'title' => self::STATUS_NEW,
            'type' => self::TYPE_ACCIDENT,
        ],
        [
            // doctor got new case
            'title' => self::STATUS_ASSIGNED,
            'type' => self::TYPE_DOCTOR,
        ],
        [
            // doctor has started this case
            'title' => self::STATUS_IN_PROGRESS,
            'type' => self::TYPE_DOCTOR,
        ],
        [
            // doctor sent case to director
            'title' => self::STATUS_SENT,
            'type' => self::TYPE_DOCTOR,
        ],
        [
            // doctor got his money
            'title' => self::STATUS_PAID,
            'type' => self::TYPE_DOCTOR,
        ],
        [
            // doctor rejected the case
            'title' => self::STATUS_REJECT,
            'type' => self::TYPE_DOCTOR,
        ],
        [
            'title' => self::STATUS_CLOSED,
            'type' => self::TYPE_ACCIDENT,
        ]
    ];

    public function set(Accident $accident, AccidentStatus $status, $comment = '')
    {
        $accident->accident_status_id = $status->id;
        $accident->save();

        event(new AccidentStatusChanged($accident, $comment));
    }
}
