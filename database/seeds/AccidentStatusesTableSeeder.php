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
        if (App::environment('production') && \App\AccidentStatus::all()->count()) {
            return;
        }
        if (!App::environment('production')) {
            AccidentStatus::truncate();
        }
        foreach (self::ACCIDENT_STATUSES as $accidentStatus) {
            AccidentStatus::firstOrCreate($accidentStatus);
        }
    }
}
