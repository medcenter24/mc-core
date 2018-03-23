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

    return [
        'name' => $faker->name,
        'description' => $faker->text(),
        'ref_key' => $faker->toUpper(str_random(3)),
        'gender' => $faker->randomElement(['male', 'female', 'none']),
        'medical_board_num' => $faker->numberBetween(1000000, 9999999),
        'city_id' => function () {
            return factory(City::class)->make()->id;
        }
    ];
});
