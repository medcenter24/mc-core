<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\Assistant::class, function (Faker $faker) {
    return [
        'title' => $faker->text(120),
        'ref_key' => str_random(3),
        'email' => $faker->email,
        'comment' => $faker->text(200),
    ];
});
