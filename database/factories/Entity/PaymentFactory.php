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
use medcenter24\mcCore\App\Entity\FinanceCurrency;
use medcenter24\mcCore\App\Entity\Payment;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    #[ArrayShape([
        'value' => "float",
        'currency_id' => "mixed",
        'fixed' => "int",
        'description' => "string"
    ])]
    public function definition(): array
    {
        return [
            'value' => $this->faker->randomFloat(0, 100),
            'currency_id' => FinanceCurrency::factory()->create()->id,
            'fixed' => 0,
            'description' => 'faked payment'
        ];
    }
}
