<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\HospitalAccident::class, function (Faker $faker) {
    return [
        'hospital_id' => $faker->numberBetween(1, 10),
        'hospital_guarantee_id' => $faker->numberBetween(1, 10),
        'hospital_invoice_id' => $faker->numberBetween(1, 10),
        'assistant_invoice_id' => $faker->numberBetween(1, 10),
        'assistant_guarantee_id' => $faker->numberBetween(1, 10),
    ];
});
