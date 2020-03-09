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


use medcenter24\mcCore\App\Entity\FinanceStorage;
use Faker\Generator as Faker;

$factory->define(FinanceStorage::class, function (Faker $faker) {
    $class = $faker->randomElement(['medcenter24\mcCore\App\DoctorService', 'medcenter24\mcCore\App\Doctor', 'medcenter24\mcCore\App\Assistant', 'medcenter24\mcCore\App\City', 'medcenter24\mcCore\App\DatePeriod']);
    $model = factory($class)->create();
    return [
        'finance_condition_id' => 0, // generates fake condition even if I try to set my own, misleading,
                                     // deprecated factory(FinanceCondition::class)->create()->id,
        'model' => $class,
        'model_id' => $model->id,
    ];
});
