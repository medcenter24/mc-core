<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\Hospital::class, function (Faker $faker) {
    return [
        'title' => $faker->text(30),
        'description' => $faker->text(),
        'ref_key' => str_random(2),
        'address' => $faker->address,
        'phones' => $faker->phoneNumber.','.$faker->phoneNumber,
    ];
});
