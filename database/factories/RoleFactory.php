<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Role;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'title' => $faker->randomElement([
            Role::ROLE_LOGIN,
            Role::ROLE_DIRECTOR,
            Role::ROLE_DOCTOR,
            Role::ROLE_ADMIN,
        ]),
    ];
});
