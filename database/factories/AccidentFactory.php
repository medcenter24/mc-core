<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Entity\AccidentType;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\FormReport;
use medcenter24\mcCore\App\Entity\Patient;
use medcenter24\mcCore\App\Services\Entity\AccidentTypeService;
use medcenter24\mcCore\App\Entity\User;
use Faker\Generator as Faker;

/**
 * Notice:
 * Everything changed to create to have real id's otherwise phpunit tests will be failed
 */
$factory->define(Accident::class, function (Faker $faker) {
    $refNum = $faker->toUpper(Str::random(3)) . '-' . $faker->toUpper(Str::random(3)) . $faker->toUpper(Str::random(3));
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
        'assistant_ref_num' => $faker->toUpper(Str::random(3)) . '-' . $faker->toUpper(Str::random(3)),
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
            return factory(City::class)->create()->id;
        },
        'address' => $faker->address,
        'contacts' => $faker->company . "\n" . $faker->companyEmail . "\n" . $faker->phoneNumber,
        'symptoms' => $faker->paragraphs(4, true),
        'handling_time' => $faker->dateTime(),
        'assistant_invoice_id' => 0,
        'assistant_guarantee_id' => 0,
    ];
});
