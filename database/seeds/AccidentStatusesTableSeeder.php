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
declare(strict_types = 1);

namespace Database\Seeders;

use Illuminate\Support\Facades\App;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
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
     * 10. accident reopened
     */
    public const ACCIDENT_STATUSES = [
        [
            'title' => AccidentStatusService::STATUS_NEW,
            'type' => AccidentStatusService::TYPE_ACCIDENT,
        ],
        [
            // doctor got new case
            'title' => AccidentStatusService::STATUS_ASSIGNED,
            'type' => AccidentStatusService::TYPE_DOCTOR,
        ],
        [
            // doctor has started this case
            'title' => AccidentStatusService::STATUS_IN_PROGRESS,
            'type' => AccidentStatusService::TYPE_DOCTOR,
        ],
        [
            // doctor sent case to director
            'title' => AccidentStatusService::STATUS_SENT,
            'type' => AccidentStatusService::TYPE_DOCTOR,
        ],
        [
            // doctor got his money
            'title' => AccidentStatusService::STATUS_PAID,
            'type' => AccidentStatusService::TYPE_DOCTOR,
        ],
        [
            // doctor rejected the case
            'title' => AccidentStatusService::STATUS_REJECT,
            'type' => AccidentStatusService::TYPE_DOCTOR,
        ],
        [
            'title' => AccidentStatusService::STATUS_CLOSED,
            'type' => AccidentStatusService::TYPE_ACCIDENT,
        ],
        [
            'title' => AccidentStatusService::STATUS_REOPENED,
            'type' => AccidentStatusService::TYPE_ACCIDENT,
        ]
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('production') && AccidentStatus::all()->count()) {
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
