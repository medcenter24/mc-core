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
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Form;

class FormFactory extends Factory
{
    protected $model = Form::class;

    #[ArrayShape([
        'title' => "string",
        'description' => "string",
        'template' => "string",
        'variables' => "string",
        'formable_type' => "string"
    ])]
    public function definition(): array
    {
        return [
            'title' => $this->faker->text(20),
            'description' => $this->faker->text(200),
            'template' => '<p>Hello :firstName, :lastName</p><p>Some text in here with :param</p>',
            'variables' => ':firstName, :lastName, :param',
            'formable_type' => Accident::class,
        ];
    }
}
