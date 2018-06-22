<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
