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

use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Doctor;
use medcenter24\mcCore\App\FinanceCondition;
use medcenter24\mcCore\App\Services\FinanceConditionService;
use Faker\Generator as Faker;

$factory->define(FinanceCondition::class, function (Faker $faker) {
    $service = new FinanceConditionService();
    return [
        'created_by' => 0, // default for the system
        'title' => 'Finance condition',
        'value' => 0, // it shouldn't influence to the other payment tests needs to be 0
        'type' => $faker->randomElement($service->getTypes()),
        'currency_id' => 0,
        'currency_mode' => $faker->randomElement(['percent', 'currency']),
        'model' => $faker->randomElement([Accident::class, Doctor::class]),
    ];
});

