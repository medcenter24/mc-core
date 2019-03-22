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

use App\City;
use App\Doctor;
use Faker\Generator as Faker;

$factory->define(Doctor::class, function (Faker $faker) {
    $refKey = $faker->toUpper(str_random(3));
    return [
        'name' => $faker->firstName,
        'description' => 'Doctor ' . $refKey,
        'ref_key' => $refKey,
        'gender' => $faker->randomElement(['male', 'female', 'none']),
        'medical_board_num' => $faker->numberBetween(1000000, 9999999),
        'city_id' => function () {
            return factory(City::class)->create()->id;
        }
    ];
});
