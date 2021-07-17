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
declare(strict_types = 1);

namespace Database\Factories\Entity;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\DoctorAccident;

class DoctorAccidentFactory extends Factory
{
    protected $model = DoctorAccident::class;

    #[ArrayShape([
        'doctor_id' => "int",
        'recommendation' => "array|string",
        'investigation' => "array|string",
        'visit_time' => "\DateTime"
    ])]
    public function definition(): array
    {
        return [
            'doctor_id' => 0,
            'recommendation' => $this->faker->paragraphs(3, true),
            'investigation' => $this->faker->paragraphs(3, true),
            'visit_time' => new DateTime(),
        ];
    }
}
