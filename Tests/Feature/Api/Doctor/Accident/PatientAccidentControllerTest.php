<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\Tests\Feature\Api\Doctor\Accident;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\Tests\TestCase;

class PatientAccidentControllerTest extends TestCase
{
    use TestDoctorAccidentTrait;

    public function testPatient(): void
    {
        /** @var Accident $case */
        $case = $this->createAccidentForDoc();
        $response = $this->sendGet('/api/doctor/accidents/' . $case->id . '/patient');
        $response->assertStatus(200);
        $response->assertJson(['data' => [
            'id' => 1,
        ]]);
    }

    public function testUpdatePatient(): void
    {
        /** @var Accident $case */
        $case = $this->createAccidentForDoc();
        $response = $this->sendPatch('/api/doctor/accidents/' . $case->id . '/patient',
            [
                'name' => 'CARLO MCKENZIE',
                'address' => '509 Wehner Landing North Mayafort, VA 23753-2717',
                'phones' => '792.744.1280',
                'birthday' => '1972-04-04',
                'comment' => 'com',
            ]);
        $response->assertStatus(200);
        $response->assertJson(['data' => [
            'id' => 1,
            'name' => 'CARLO MCKENZIE',
            'address' => '509 Wehner Landing North Mayafort, VA 23753-2717',
            'phones' => '792.744.1280',
            'birthday' => '1972-04-04',
            'comment' => 'com',
        ]]);
    }
}
