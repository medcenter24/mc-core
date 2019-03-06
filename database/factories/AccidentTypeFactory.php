<?php

use Faker\Generator as Faker;

$factory->define(App\AccidentType::class, function (Faker $faker) {
    return [
        'title' => $faker->randomElement(\App\Services\AccidentTypeService::ALLOWED_TYPES),
        'description' => $faker->text(),
    ];
});
