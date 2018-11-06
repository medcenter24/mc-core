<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\Invoice::class, function (Faker $faker) {
    return [
        'created_by' => function () {
            return factory(\App\User::class)->create()->id;
        },
        'title' => $faker->text(30),
        'payment_id' => function () {
            return factory(\App\Payment::class)->create()->id;
        },
        'status' => 'new',
    ];
});
