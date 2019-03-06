<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\DoctorService::class, function (Faker $faker) {
    $refKey = $faker->toUpper(str_random(3));
    return [
        'title' => 'DoctorService ' . $refKey,
        'description' => $faker->text(),
        'disease_code' => $refKey,
    ];
});
