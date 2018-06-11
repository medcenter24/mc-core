<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\Assistant::class, function (Faker $faker) {
    $refKey = $faker->toUpper(str_random(3));
    return [
        'title' => 'Assistant ' . $refKey,
        'ref_key' => $refKey,
        'email' => $faker->email,
        'comment' => $faker->text(200),
    ];
});
