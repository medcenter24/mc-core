<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

use App\Accident;
use App\Doctor;
use App\FinanceCurrency;
use App\Services\FinanceConditionService;
use Faker\Generator as Faker;

$factory->define(FinanceCurrency::class, function (Faker $faker, FinanceConditionService $financeService) {
    return [
        'created_by' => 0, // default for the system
        'title' => 'Finance condition',
        'price' => mt_rand(1, 999),
        'type' => $faker->randomElement($financeService->getTypes()),
        'currency' => $faker->randomElement($financeService->getCurrencies()),
        'model' => $faker->randomElement([Accident::class, Doctor::class]),
    ];
});
