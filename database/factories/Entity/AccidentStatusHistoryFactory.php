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

namespace Database\Factories\Entity;

use Database\Seeders\AccidentStatusesTableSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Entity\AccidentStatusHistory;

class AccidentStatusHistoryFactory extends Factory
{
    protected $model = AccidentStatusHistory::class;

    #[ArrayShape([
        'commentary' => "string",
        'accident_status_id' => "mixed",
        'historyable_id' => "\Closure",
        'historyable_type' => "string"
    ])]
    public function definition(): array
    {
        return [
            'commentary' => $this->faker->text(20),
            'accident_status_id' => AccidentStatus::firstOrCreate(AccidentStatusesTableSeeder::ACCIDENT_STATUSES[0])->id,
            'historyable_id' => Accident::factory()->create()->id,
            'historyable_type' => Accident::class,
        ];
    }
}
