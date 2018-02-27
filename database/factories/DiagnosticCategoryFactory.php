<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\DiagnosticCategory;
use Faker\Generator as Faker;

$factory->define(DiagnosticCategory::class, function (Faker $faker) {
    return [
        'title' => $faker->text(70),
    ];
});
