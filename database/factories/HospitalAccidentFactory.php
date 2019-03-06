<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\HospitalAccident::class, function (Faker $faker) {
    return [
        'hospital_id' => 0,
        'hospital_guarantee_id' => 0, //
        'hospital_invoice_id' => 0, // don't need to be set for unittests
    ];
});
