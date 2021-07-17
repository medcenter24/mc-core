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

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\Hospital;

class HospitalFactory extends Factory
{
    protected $model = Hospital::class;

    #[ArrayShape([
        'title' => "string",
        'description' => "string",
        'ref_key' => "string",
        'address' => "string",
        'phones' => "string"
    ])]
    public function definition(): array
    {
        return [
            'title' => $this->faker->text(30),
            'description' => $this->faker->text(),
            'ref_key' => Str::random(2),
            'address' => $this->faker->address,
            'phones' => $this->faker->phoneNumber.','.$this->faker->phoneNumber,
        ];
    }
}
