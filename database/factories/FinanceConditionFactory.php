<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

use App\Accident;
use App\Doctor;
use App\FinanceCondition;
use App\Services\FinanceService;
use Faker\Generator as Faker;

$factory->define(FinanceCondition::class, function (Faker $faker) {
    return [
        'created_by' => 0, // default for the system
        'title' => 'Finance condition',
        'value' => mt_rand(1, 999),
        'type' => $faker->randomElement(FinanceService::getTypes()),
        'currency_id' => 0,
        'currency_mode' => 'percent',
        'model' => $faker->randomElement([Accident::class, Doctor::class]),
    ];
});

