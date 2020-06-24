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

use Faker\Generator as Faker;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentStatusHistory;

$factory->define(AccidentStatusHistory::class, function (Faker $faker) {
    return [
        'commentary' => $faker->text(20),
        'accident_status_id' => function () use ($faker) {
            return getRandomAccidentStatus($faker)->id;
        },
        'historyable_id' => function () {
            // could be each of accident Doctor_Accident Accident Hospital_Accident ...
            return factory(Accident::class)->create()->id;
        },
        'historyable_type' => Accident::class,
    ];
});
