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
        'price' => $faker->randomFloat(2, 0, 10000),
    ];
});
