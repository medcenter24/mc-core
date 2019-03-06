<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Accident;
use App\User;
use Faker\Generator as Faker;

$factory->define(App\Comment::class, function (Faker $faker) {
    return [
        'created_by' => function () {
            return factory(User::class)->create()->id;
        },
        'text' => $faker->paragraphs(3, true),
        'commentable_type' => Accident::class,
        'commentable_id' => function () {
            return factory(Accident::class)->create()->id;
        },
    ];
});
