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

$factory->define(FinanceCondition::class, function (Faker $faker, FinanceService $financeService) {
    return [
        'created_by' => 0, // default for the system
        'title' => 'Finance condition',
        'price' => mt_rand(1, 999),
        'type' => $faker->randomElement($financeService->getTypes()),
        'currency' => $faker->randomElement($financeService->getCurrencies()),
        'model' => $faker->randomElement([Accident::class, Doctor::class]),
    ];
});
