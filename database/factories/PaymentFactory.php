<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\Payment::class, function (Faker $faker) {
    return [
        'value' => $faker->randomFloat(0, 10000),
        'currency_id' => function () {
            return factory(\App\FinanceCurrency::class)->create()->id;
        },
        'fixed' => 0,
        'description' => 'Faker factory',
    ];
});
