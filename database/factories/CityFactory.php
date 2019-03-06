<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\City::class, function (Faker $faker) {
    return [
        'title' => $faker->city,
    ];
});
