<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Faker\Generator as Faker;

$factory->define(App\Patient::class, function (Faker $faker) {
    return [
        'name' => $faker->toUpper($faker->firstName . ' ' . $faker->lastName),
        'address' => $faker->address,
        'phones' => $faker->phoneNumber,
        'birthday' => $faker->date(),
        'comment' => $faker->text(200),
    ];
});
