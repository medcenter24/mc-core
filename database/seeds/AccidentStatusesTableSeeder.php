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
            'title' => AccidentStatusesService::STATUS_NEW,
            'type' => AccidentStatusesService::TYPE_ACCIDENT,
        ],
        [
            // doctor got new case
            'title' => AccidentStatusesService::STATUS_ASSIGNED,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ],
        [
            // doctor has started this case
            'title' => AccidentStatusesService::STATUS_IN_PROGRESS,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ],
        [
            // doctor sent case to director
            'title' => AccidentStatusesService::STATUS_SENT,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ],
        [
            // doctor got his money
            'title' => AccidentStatusesService::STATUS_PAID,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ],
        [
            // doctor rejected the case
            'title' => AccidentStatusesService::STATUS_REJECT,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ],
        [
            'title' => AccidentStatusesService::STATUS_CLOSED,
            'type' => AccidentStatusesService::TYPE_ACCIDENT,
        ]
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV') == 'production' && \App\AccidentStatus::all()->count()) {
            return;
        }
        if (env('APP_ENV') != 'production') {
            AccidentStatus::truncate();
        }
        foreach (self::ACCIDENT_STATUSES as $accidentStatus) {
            AccidentStatus::firstOrCreate($accidentStatus);
        }
    }
}
