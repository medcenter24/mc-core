<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

use medcenter24\mcCore\App\AccidentStatus;
use medcenter24\mcCore\App\DoctorAccident;
use medcenter24\mcCore\App\HospitalAccident;
use medcenter24\mcCore\App\Scenario;
use medcenter24\mcCore\App\Services\AccidentStatusesService;
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
            'mode' => 'skip:' . AccidentStatusesService::TYPE_DOCTOR,
            // doesn't used by the scenario but a doctor needs to have opportunity to set this step in any time that he wants
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
        /** guarantee received from the assistant */
        2 => [
            'order' => 2,
            'title' => AccidentStatusesService::STATUS_ASSISTANT_GUARANTEE,
            'type' => AccidentStatusesService::TYPE_ASSISTANT,
        ],
        /** Hospital assigned */
        3 => [
            'order' => 3,
            'title' => AccidentStatusesService::STATUS_ASSIGNED,
            'type' => AccidentStatusesService::TYPE_HOSPITAL,
        ],
        /** guarantee created and sent to hospital */
        4 => [
            'order' => 4,
            'title' => AccidentStatusesService::STATUS_HOSPITAL_GUARANTEE,
            'type' => AccidentStatusesService::TYPE_HOSPITAL,
        ],
        /** invoice received from the hospital */
        5 => [
            'order' => 5,
            'title' => AccidentStatusesService::STATUS_HOSPITAL_INVOICE,
            'type' => AccidentStatusesService::TYPE_HOSPITAL,
        ],
        /** Invoice sent to the assistant */
        6 => [
            'order' => 6,
            'title' => AccidentStatusesService::STATUS_ASSISTANT_INVOICE,
            'type' => AccidentStatusesService::TYPE_ASSISTANT,
        ],
        7 => [
            'order' => 7,
            'title' => AccidentStatusesService::STATUS_PAID,
            'type' => AccidentStatusesService::TYPE_ASSISTANT,
        ],
        8 => [
            'order' => 8,
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
        if (App::environment('production') && Scenario::all()->count()) {
            return;
        }
        if (!App::environment('production')) {
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
