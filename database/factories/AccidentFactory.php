<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentStatus;
use App\AccidentType;
use App\Assistant;
use App\DoctorAccident;
use App\FormReport;
use App\Patient;
use App\Services\AccidentTypeService;
use App\User;
use Faker\Generator as Faker;

/**
 * Notice:
 * Everything changed to create to have real id's otherwise phpunit tests will be failed
 */
$factory->define(\App\Accident::class, function (Faker $faker) {
    $refNum = $faker->toUpper(str_random(3)) . '-' . $faker->toUpper(str_random(3)) . $faker->toUpper(str_random(3));
    return [
        'created_by' => function () {
            return factory(User::class)->create()->id;
        },
        'parent_id' => 0,
        'patient_id' => function () {
            return factory(Patient::class)->create()->id;
        },
        'accident_type_id' => function () use ($faker) {
            $type = $faker->randomElement(AccidentTypeService::ALLOWED_TYPES);
            $el = AccidentType::where(['title' => $type])->first();
            return $el ? $el->id : factory(AccidentType::class)->create(['title' => $type])->id;
        },
        'accident_status_id' => function () use ($faker) {
            $status = $faker->randomElement(AccidentStatusesTableSeeder::ACCIDENT_STATUSES);
            $el = AccidentStatus::where($status)->first();
            return $el ? $el->id : factory(AccidentStatus::class)->create($status)->id;
        },
        'assistant_id' => function () {
            return factory(Assistant::class)->create()->id;
        },
        'assistant_ref_num' => $faker->toUpper(str_random(3)) . '-' . $faker->toUpper(str_random(3)),
        'caseable_id' => function () {
            return factory(DoctorAccident::class)->create()->id;
        },
        'form_report_id' => function () {
            return factory(FormReport::class)->create()->id;
        },
        'caseable_type' => DoctorAccident::class,
        'ref_num' => $refNum,
        'title' => 'Accident ' . $refNum,
        'city_id' => function () {
            return factory(\App\City::class)->create()->id;
        },
        'address' => $faker->address,
        'contacts' => $faker->company . "\n" . $faker->companyEmail . "\n" . $faker->phoneNumber,
        'symptoms' => $faker->paragraphs(4, true),
        'handling_time' => $faker->dateTime(),
    ];
});
