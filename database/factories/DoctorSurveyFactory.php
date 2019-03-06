<?php

use Faker\Generator as Faker;

$factory->define(App\DoctorSurvey::class, function (Faker $faker) {
    return [
        'title' => $faker->text(20),
        'description' => $faker->text(),
    ];
});
