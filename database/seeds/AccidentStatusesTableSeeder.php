<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentStatus;
use App\Services\AccidentStatusesService;
use Illuminate\Database\Seeder;

class AccidentStatusesTableSeeder extends Seeder
{
    const TYPE_ACCIDENT = AccidentStatusesService::TYPE_ACCIDENT;
    const TYPE_DOCTOR = AccidentStatusesService::TYPE_DOCTOR;
    const TYPE_HOSPITAL = AccidentStatusesService::TYPE_HOSPITAL;

    const STATUS_NEW = AccidentStatusesService::STATUS_NEW;
    const STATUS_ASSIGNED = AccidentStatusesService::STATUS_ASSIGNED;
    const STATUS_IN_PROGRESS = AccidentStatusesService::STATUS_IN_PROGRESS;
    const STATUS_SENT = AccidentStatusesService::STATUS_SENT;
    const STATUS_PAID = AccidentStatusesService::STATUS_PAID;
    const STATUS_REJECT = AccidentStatusesService::STATUS_REJECT;
    const STATUS_CLOSED = AccidentStatusesService::STATUS_CLOSED;

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


    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ErrorException
     */
    public function run()
    {
        if (env('APP_ENV') == 'production' && \App\AccidentStatus::all()->count()) {
            throw new ErrorException('Production database can not be truncated');
        }
        // but on the dev it can be deleted
        AccidentStatus::truncate();
        foreach (self::ACCIDENT_STATUSES as $accidentStatus) {
            AccidentStatus::firstOrCreate($accidentStatus);
        }
    }
}
