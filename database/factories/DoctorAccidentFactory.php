<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\DoctorAccident::class, function (Faker $faker) {
    return [
        'doctor_id' => $faker->numberBetween(1, 10),
        'recommendation' => $faker->paragraphs(3, true),
        'investigation' => $faker->paragraphs(3, true),
        'visit_time' => new DateTime(),
    ];
});
