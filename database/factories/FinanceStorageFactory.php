<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */


use App\FinanceCondition;
use App\FinanceStorage;
use Faker\Generator as Faker;

$factory->define(FinanceStorage::class, function (Faker $faker) {
    $class = $faker->randomElement(['App\DoctorService', 'App\Doctor', 'App\Assistant', 'App\City', 'App\DatePeriod']);
    $model = factory($class)->create();
    return [
        'finance_condition_id' => 0, // generates fake condition even if I try to set my own, misleading,
                                    // deprecated factory(FinanceCondition::class)->create()->id,
        'model' => $class,
        'model_id' => $model->id,
    ];
});
