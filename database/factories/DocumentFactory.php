<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\User;
use Faker\Generator as Faker;

$factory->define(App\Document::class, function (Faker $faker) {
    return [
        'created_by' => function () {
            return factory(User::class)->create()->id;
        },
        'title' => $faker->title,
    ];
});
