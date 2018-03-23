<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\AccidentStatusHistory::class, function (Faker $faker) {
    return [
        'commentary' => $faker->text(20),
        'accident_status_id' => function () use ($faker) {
            return getRandomAccidentStatus($faker)->id;
        },
        'historyable_id' => function () {
            // could be each of accident Doctor_Accident Accident Hospital_Accident ...
            return factory(\App\Accident::class)->make()->id;
        },
        'historyable_type' => \App\Accident::class,
    ];
});
