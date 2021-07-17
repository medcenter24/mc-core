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

use Database\Seeders\AccidentStatusesTableSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
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

/**
 * Notice:
 * Everything changed to create to have real id's otherwise phpunit tests will be failed
 */
class AccidentFactory extends Factory
{
    protected $model = Accident::class;

    public function definition(): array
    {
        $refNum = $this->faker->toUpper(Str::random(3))
            . '-' . $this->faker->toUpper(Str::random(3))
            . $this->faker->toUpper(Str::random(3));

        return [
            'created_by' => function () {
                return User::factory()->create()->id;
            },
            'parent_id' => 0,
            'patient_id' => function () {
                return Patient::factory()->create()->id;
            },
            'accident_type_id' => function () {
                $type = $this->faker->randomElement(AccidentTypeService::ALLOWED_TYPES);
                $el = AccidentType::where(['title' => $type])->first();
                return $el ? $el->id : AccidentType::factory()->create(['title' => $type])->id;
            },
            'accident_status_id' => function () {
                $status = $this->faker->randomElement(AccidentStatusesTableSeeder::ACCIDENT_STATUSES);
                $el = AccidentStatus::where($status)->first();
                return $el ? $el->id : AccidentStatus::factory()->create($status)->id;
            },
            'assistant_id' => Assistant::factory()->create()->id,
            'assistant_ref_num' => $this->faker->toUpper(Str::random(3))
                . '-' . $this->faker->toUpper(Str::random(3)),
            'caseable_id' => DoctorAccident::factory()->create()->id,
            'form_report_id' => FormReport::factory()->create()->id,
            'caseable_type' => DoctorAccident::class,
            'ref_num' => $refNum,
            'title' => 'Accident ' . $refNum,
            'city_id' => City::factory()->create()->id,
            'address' => $this->faker->address,
            'contacts' => $this->faker->company . "\n" . $this->faker->companyEmail . "\n" . $this->faker->phoneNumber,
            'symptoms' => $this->faker->paragraphs(4, true),
            'handling_time' => $this->faker->dateTime(),
            'assistant_invoice_id' => 0,
            'assistant_guarantee_id' => 0,
        ];
    }
}
