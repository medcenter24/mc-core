<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\AccidentStatus::class, function (Faker $faker) {
    $status = $faker->randomElement(AccidentStatusesTableSeeder::ACCIDENT_STATUSES);

    return [
        'title' => $status['title'],
        'type' => $status['type'],
    ];
});
