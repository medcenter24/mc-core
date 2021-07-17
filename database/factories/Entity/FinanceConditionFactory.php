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

use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\FinanceCondition;
use medcenter24\mcCore\App\Services\Entity\FinanceConditionService;

class FinanceConditionFactory extends Factory
{
    protected $model = FinanceCondition::class;

    #[ArrayShape([
        'created_by' => "int",
        'title' => "string",
        'value' => "int",
        'type' => "mixed",
        'currency_id' => "int",
        'currency_mode' => "mixed",
        'model' => "mixed"
    ])] public function definition(): array
    {
        $service = new FinanceConditionService();
        return [
            'created_by' => 0, // default for the system
            'title' => 'Finance condition',
            'value' => 0, // it shouldn't influence to the other payment tests needs to be 0
            'type' => $this->faker->randomElement($service->getTypes()),
            'currency_id' => 0,
            'currency_mode' => $this->faker->randomElement(['percent', 'currency']),
            'model' => $this->faker->randomElement([Accident::class, Doctor::class]),
        ];
    }
}
