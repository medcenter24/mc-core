<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Illuminate\Database\Seeder;

class ScenariosTableSeeder extends Seeder
{
    const DEFAULT_MODE = 'step';
    const DOCTOR_SCENARIO = [
        1 => [
            'order' => 1,
            'title' => AccidentStatusesTableSeeder::STATUS_NEW,
            'type' => AccidentStatusesTableSeeder::TYPE_ACCIDENT,
        ],
        2 => [
            'order' => 2,
            'title' => AccidentStatusesTableSeeder::STATUS_ASSIGNED,
            'type' => AccidentStatusesTableSeeder::TYPE_DOCTOR,
        ],
        3 => [
            'order' => 3,
            'title' => AccidentStatusesTableSeeder::STATUS_IN_PROGRESS,
            'type' => AccidentStatusesTableSeeder::TYPE_DOCTOR,
        ],
        4 => [
            'order' => 4,
            'title' => AccidentStatusesTableSeeder::STATUS_SENT,
            'type' => AccidentStatusesTableSeeder::TYPE_DOCTOR,
        ],
        5 => [
            'order' => 5,
            'title' => AccidentStatusesTableSeeder::STATUS_PAID,
            'type' => AccidentStatusesTableSeeder::TYPE_DOCTOR,
        ],
        6 => [
            'order' => 6,
            'title' => AccidentStatusesTableSeeder::STATUS_REJECT,
            'type' => AccidentStatusesTableSeeder::TYPE_DOCTOR,
            'mode' => 'skip:' . AccidentStatusesTableSeeder::TYPE_DOCTOR, // don't used by
            // scenario but doctor could set this step in any time that he want
            // skip doctor means that all type doctor will be skipped after that step
        ],
        7 => [
            'order' => 7,
            'title' => AccidentStatusesTableSeeder::STATUS_CLOSED,
            'type' => AccidentStatusesTableSeeder::TYPE_ACCIDENT,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ErrorException
     */
    public function run()
    {
        if (env('APP_ENV') == 'production' && \App\Scenario::all()->count()) {
            throw new ErrorException('Production database can not be truncated');
        }

        \App\Scenario::truncate();
        foreach (self::DOCTOR_SCENARIO as $step) {
            factory(\App\Scenario::class)->create([
                'accident_status_id' => \App\AccidentStatus::firstOrCreate([
                    'title' => $step['title'],
                    'type' => $step['type'],
                ]),
                'tag' => \App\DoctorAccident::class,
                'order' => $step['order'],
                'mode' => array_key_exists('mode', $step) ? $step['mode'] : self::DEFAULT_MODE,
            ]);
        }
    }
}
