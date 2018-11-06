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

$factory->define(FinanceCurrency::class, function (Faker $faker) {
    $currency = $faker->currencyCode;
    return [
        'title' => $currency,
        'code' => $currency,
        'ico' => 'fa fa-'.$currency,
    ];
});
