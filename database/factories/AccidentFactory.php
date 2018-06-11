<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentType;
use App\Assistant;
use App\DoctorAccident;
use App\FormReport;
use App\Patient;
use App\Services\AccidentTypeService;
use App\User;
use Faker\Generator as Faker;

$factory->define(\App\Accident::class, function (Faker $faker) {
    $refNum = $faker->toUpper(str_random(3)) . '-' . $faker->toUpper(str_random(3)) . $faker->toUpper(str_random(3));
    return [
        'created_by' => function () {
            return factory(User::class)->make()->id;
        },
        'parent_id' => 0,
        'patient_id' => function () {
            return factory(Patient::class)->make()->id;
        },
        'accident_type_id' => function () use ($faker) {
            $type = $faker->randomElement(AccidentTypeService::ALLOWED_TYPES);
            return factory(AccidentType::class)->make(['title' => $type])->id;
        },
        'accident_status_id' => function () use ($faker) {
            $status = $faker->randomElement(AccidentStatusesTableSeeder::ACCIDENT_STATUSES);
            return factory(\App\AccidentStatus::class)->make($status)->id;
        },
        'assistant_id' => function () {
            return factory(Assistant::class)->make()->id;
        },
        'assistant_ref_num' => $faker->toUpper(str_random(3)) . '-' . $faker->toUpper(str_random(3)),
        'caseable_id' => function () {
            return factory(DoctorAccident::class)->make()->id;
        },
        'form_report_id' => function () {
            return factory(FormReport::class)->make()->id;
        },
        'caseable_type' => DoctorAccident::class,
        'ref_num' => $refNum,
        'title' => 'Accident ' . $refNum,
        'city_id' => function () {
            return factory(\App\City::class)->make()->id;
        },
        'address' => $faker->address,
        'contacts' => $faker->company . "\n" . $faker->companyEmail . "\n" . $faker->phoneNumber,
        'symptoms' => $faker->paragraphs(4, true),
        'handling_time' => $faker->dateTime(),
    ];
});
