<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

use App\Accident;
use App\Doctor;
use App\FinanceCondition;
use App\Services\FinanceConditionService;
use Faker\Generator as Faker;

$factory->define(FinanceCondition::class, function (Faker $faker) {
    $service = new FinanceConditionService();
    return [
        'created_by' => 0, // default for the system
        'title' => 'Finance condition',
        'value' => 0, // it shouldn't influence to the other payment tests needs to be 0
        'type' => $faker->randomElement($service->getTypes()),
        'currency_id' => 0,
        'currency_mode' => $faker->randomElement(['percent', 'currency']),
        'model' => $faker->randomElement([Accident::class, Doctor::class]),
    ];
});

