<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\Upload::class, function (Faker $faker) {
    return [
        'value' => $faker->randomFloat(0, 100),
        'currency_id' => factory(\App\FinanceCurrency::class)->create()->id,
        'fixed' => 0,
        'description' => 'faked payment'
    ];
});
