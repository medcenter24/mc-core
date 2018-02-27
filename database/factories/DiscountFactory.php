<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Services\DiscountService;
use Faker\Generator as Faker;

$factory->define(App\Discount::class, function (Faker $faker) {
    return [
        'title' => $faker->text(20),
        'description' => $faker->text(200),
        'operation' => $faker->randomElement(DiscountService::ALLOWED_OPERATIONS),
    ];
});
