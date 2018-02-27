<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentType;
use App\Assistant;
use App\Discount;
use App\DoctorAccident;
use App\FormReport;
use App\Patient;
use App\Services\AccidentTypeService;
use App\Services\DiscountService;
use App\User;
use Faker\Generator as Faker;

$factory->define(\App\Accident::class, function (Faker $faker) {

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
            $accidentType = AccidentType::where('title', $type)->first();
            return $accidentType
                ? $accidentType->id
                : factory(AccidentType::class)->create(['title' => $type])->id;
        },
        'accident_status_id' => function () use ($faker) {
            return getRandomAccidentStatus($faker)->id;
        },
        'assistant_id' => function () {
            return factory(Assistant::class)->create()->id;
        },
        'assistant_ref_num' => $faker->text(20),
        'caseable_id' => function () {
            return factory(DoctorAccident::class)->create()->id;
        },
        'form_report_id' => function () {
            return factory(FormReport::class)->create()->id;
        },
        'caseable_type' => DoctorAccident::class,
        'ref_num' => str_random(3) . '-' . $faker->numberBetween('100', '999') . '-' . str_random(2),
        'title' => $faker->text(30),
        'city_id' => function () {
            return factory(\App\City::class)->create()->id;
        },
        'address' => $faker->address,
        'contacts' => $faker->company . "\n" . $faker->companyEmail . "\n" . $faker->phoneNumber,
        'symptoms' => $faker->paragraphs(4, true),
        'discount_id' => function () use ($faker) {
            $type = $faker->randomElement(DiscountService::ALLOWED_OPERATIONS);
            $discount = Discount::where('operation', $type)->first();
            return $discount
                ? $discount->id
                : factory(Discount::class)->create()->id;
        },
        'discount_value' => $faker->numberBetween(0, 100),
        'handling_time' => $faker->dateTime(),
    ];
});
