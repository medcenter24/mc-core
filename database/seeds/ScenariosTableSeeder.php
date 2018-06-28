<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentStatus;
use App\DoctorAccident;
use App\HospitalAccident;
use App\Scenario;
use App\Services\AccidentStatusesService;
use Illuminate\Database\Seeder;

class ScenariosTableSeeder extends Seeder
{
    const DEFAULT_MODE = 'step';
    const DOCTOR_SCENARIO = [
        1 => [
            'order' => 1,
            'title' => AccidentStatusesService::STATUS_NEW,
            'type' => AccidentStatusesService::TYPE_ACCIDENT,
        ],
        2 => [
            'order' => 2,
            'title' => AccidentStatusesService::STATUS_ASSIGNED,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ],
        3 => [
            'order' => 3,
            'title' => AccidentStatusesService::STATUS_IN_PROGRESS,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ],
        4 => [
            'order' => 4,
            'title' => AccidentStatusesService::STATUS_SENT,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ],
        5 => [
            'order' => 5,
            'title' => AccidentStatusesService::STATUS_PAID,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ],
        6 => [
            'order' => 6,
            'title' => AccidentStatusesService::STATUS_REJECT,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
            'mode' => 'skip:' . AccidentStatusesService::TYPE_DOCTOR, // don't used by
            // scenario but doctor could set this step in any time that he want
            // skip doctor means that all type doctor will be skipped after that step
        ],
        7 => [
            'order' => 7,
            'title' => AccidentStatusesService::STATUS_CLOSED,
            'type' => AccidentStatusesService::TYPE_ACCIDENT,
        ],
    ];

    const HOSPITAL_SCENARIO = [
        /** hospital case created */
        1 => [
            'order' => 1,
            'title' => AccidentStatusesService::STATUS_NEW,
            'type' => AccidentStatusesService::TYPE_ACCIDENT,
        ],
        /** guarantee created and sent to hospital */
        2 => [
            'order' => 2,
            'title' => AccidentStatusesService::STATUS_HOSPITAL_GUARANTEE,
            'type' => AccidentStatusesService::TYPE_HOSPITAL,
        ],
        /** invoice received from the hospital */
        3 => [
            'order' => 3,
            'title' => AccidentStatusesService::STATUS_HOSPITAL_INVOICE,
            'type' => AccidentStatusesService::TYPE_HOSPITAL,
        ],
        /** Invoice sent to the assistant */
        4 => [
            'order' => 4,
            'title' => AccidentStatusesService::STATUS_ASSISTANT_INVOICE,
            'type' => AccidentStatusesService::TYPE_ASSISTANT,
        ],
        /** guarantee received from the assistant */
        5 => [
            'order' => 5,
            'title' => AccidentStatusesService::STATUS_ASSISTANT_GUARANTEE,
            'type' => AccidentStatusesService::TYPE_ASSISTANT,
        ],
        6 => [
            'order' => 6,
            'title' => AccidentStatusesService::STATUS_PAID,
            'type' => AccidentStatusesService::TYPE_ASSISTANT,
        ],
        7 => [
            'order' => 7,
            'title' => AccidentStatusesService::STATUS_CLOSED,
            'type' => AccidentStatusesService::TYPE_ACCIDENT,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV') == 'production' && Scenario::all()->count()) {
            return;
        }
        if (env('APP_ENV') != 'production') {
            Scenario::truncate();
        }
        foreach (self::DOCTOR_SCENARIO as $step) {
            Scenario::firstOrCreate([
                'accident_status_id' => AccidentStatus::firstOrCreate([
                    'title' => $step['title'],
                    'type' => $step['type'],
                ])->id,
                'tag' => DoctorAccident::class,
                'order' => $step['order'],
                'mode' => array_key_exists('mode', $step) ? $step['mode'] : self::DEFAULT_MODE,
            ]);
        }

        foreach (self::HOSPITAL_SCENARIO as $step) {
            Scenario::firstOrCreate([
                'accident_status_id' => AccidentStatus::firstOrCreate([
                    'title' => $step['title'],
                    'type' => $step['type'],
                ])->id,
                'tag' => HospitalAccident::class,
                'order' => $step['order'],
                'mode' => array_key_exists('mode', $step) ? $step['mode'] : self::DEFAULT_MODE,
            ]);
        }
    }
}
