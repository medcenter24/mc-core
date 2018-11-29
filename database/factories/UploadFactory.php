<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\Upload::class, function (Faker $faker) {
    return [
        'uploadable_type' => 'App\User',
        'uploadable_id' => 1,
    ];
});
