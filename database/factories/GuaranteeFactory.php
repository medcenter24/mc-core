<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\FormReport;
use Faker\Generator as Faker;

$factory->define(App\Guarantee::class, function (Faker $faker) {
    return [
        'title' => $faker->text(30),
        'status' => 'new',
    ];
});
