<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(\App\DatePeriodInterpretation::class, function (Faker $faker) {
    return [
        'day_of_week',
        'from',
        'to'
    ];
});
