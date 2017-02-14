<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(\App\Role::class, function () {
    return [
        'title' => \App\Role::ROLE_LOGIN,
    ];
});

$factory->define(\App\Doctor::class, function (\Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'description' => $faker->text(),
        'ref_key' => str_random(3),
    ];
});

$factory->define(\App\AccidentDoctor::class, function (\Faker\Generator $faker) {

    return [
        'doctor_id' => $faker->numberBetween(1,10),
        'city_id' => $faker->numberBetween(1,10),
        'status' => \App\AccidentDoctor::STATUS_NEW,
    ];
});

$factory->define(\App\Document::class, function (\Faker\Generator $faker) {

    return [
        // mock for user
        'created_by' => 1,
        'title' => $faker->title,
    ];
});
