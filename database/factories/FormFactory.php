<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Accident;
use App\Form;
use Faker\Generator as Faker;

$factory->define(Form::class, function (Faker $faker) {
    return [
        'title' => $faker->text(20),
        'description' => $faker->text(200),
        'template' => '<p>Hello :firstName, :lastName</p><p>Some text in here with :param</p>',
        'variables' => [':firstName',':lastName',[':param']],
        'formable_type' => Accident::class,
    ];
});
