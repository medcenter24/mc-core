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

$factory->define(\App\DoctorAccident::class, function (\Faker\Generator $faker) {

    return [
        'doctor_id' => $faker->numberBetween(1, 10),
        'city_id' => $faker->numberBetween(1, 10),
        'status' => \App\DoctorAccident::STATUS_NEW,
        'diagnose' => $faker->paragraphs(3, true),
    ];
});

$factory->define(\App\Document::class, function (\Faker\Generator $faker) {

    return [
        // mock for user
        'created_by' => 1,
        'title' => $faker->title,
    ];
});

$factory->define(\App\Assistant::class, function (\Faker\Generator $faker) {

    return [
        'title' => $faker->text(120),
        'ref_key' => str_random(3),
        'email' => $faker->email,
        'comment' => $faker->text(200),
    ];
});

$factory->define(\App\AccidentCheckpoint::class, function (\Faker\Generator $faker) {

    return [
        'title' => $faker->text(120),
        'description' => $faker->text(),
    ];
});

$factory->define(\App\Form::class, function (\Faker\Generator $faker) {

    return [
        'title' => $faker->text(20),
        'description' => $faker->text(200),
        'template' => '<p>Hello :firstName, :lastName</p><p>Some text in here with :param</p>',
        'variables' => 'firstName,lastName,param',
    ];
});

$factory->define(\App\FormReport::class, function (\Faker\Generator $faker) {

    return [
        'form_id' => function () {
            return factory(\App\Form::class)->create([
                'title' => 'Accident Report Form',
                'description' => 'Form has been generated by ModelFactory for FormReport',
                'template' => '<p>Form for :className</p><p>Generated by <b>:generatorName</b></p>',
                'variables' => 'className,generatorName',
            ])->id;
        },
        'values' => json_encode(['className' => \App\FormReport::class, 'generatorName' => 'ModelFactory']),
    ];
});

$factory->define(\App\Diagnostic::class, function (\Faker\Generator $faker) {

    return [
        'title' => $faker->text(20),
        'description' => $faker->text(),
    ];
});
