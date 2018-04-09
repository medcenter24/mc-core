<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\DatePeriod::class, function (Faker $faker) {

    $periodService = new \App\Services\DatePeriodService();
    $dows = $periodService->getDows();
    $dows[] = ''; // without
    return [
        'title' => $faker->text(20),
        'from' => trim($faker->randomElement($dows) . ' ' . $faker->time('H:i')),
        'to' => trim($faker->randomElement($dows) . ' ' . $faker->time('H:i')),
    ];
});

