<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\DoctorService::class, function (Faker $faker) {
    return [
        'title' => $faker->text(20),
        'description' => $faker->text(),
        'disease_code' => str_random(3),
    ];
});
