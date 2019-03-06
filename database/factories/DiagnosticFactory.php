<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\DiagnosticCategory;
use Faker\Generator as Faker;

$factory->define(App\Diagnostic::class, function (Faker $faker) {
    $refKey = $faker->toUpper(str_random(3));
    return [
        'title' => 'Diagnostic ' . $refKey,
        'disease_code' => $refKey,
        'description' => $faker->text(),
        'diagnostic_category_id' => function () {
            return factory(DiagnosticCategory::class)->create()->id;
        }
    ];
});
