<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\AccidentCheckpoint::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'description' => $faker->text(),
    ];
});