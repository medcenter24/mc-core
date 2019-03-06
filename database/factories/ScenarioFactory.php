<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\Scenario::class, function (Faker $faker) {
    return [
        'tag' => $faker->word,
        'order' => 0,
        'mode' => ScenariosTableSeeder::DEFAULT_MODE,
        'accident_status_id' => function () use ($faker) {
            return getRandomAccidentStatus($faker)->id;
        }
    ];
});
