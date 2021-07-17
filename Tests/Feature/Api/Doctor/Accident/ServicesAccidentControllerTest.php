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
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\DiagnosticService;
use medcenter24\mcCore\App\Services\Entity\DoctorAccidentService;
use medcenter24\mcCore\Tests\TestCase;

class ServicesAccidentControllerTest extends TestCase
{
    use TestDoctorAccidentTrait;

    public function testServices(): void
    {
        $accident = $this->createAccidentForDoc();
        $response = $this->sendGet('/api/doctor/accidents/'.$accident->id.'/services');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [],
        ]);
    }

    public function testCreateService(): void
    {
        $accident = $this->createAccidentForDoc();
        $response = $this->sendPost('/api/doctor/accidents/'.$accident->id.'/services', []);
        $response->assertStatus(202);
        $response->assertJson([
            'id' => 1,
            'title' => '',
            'description' => '',
            'diseases' => [],
            'status' => 'active',
            'type' => 'doctor',
        ]);
    }

    public function testCreateServiceFilled(): void
    {
        $accident = $this->createAccidentForDoc();
        $response = $this->sendPost('/api/doctor/accidents/'.$accident->id.'/services', [
            'title' => 'tit',
            'description' => 'desc',
            'diseaseId' => 1,
            'status' => 'disabled',
            'type' => 'director',
        ]);
        $response->assertStatus(202);
        $response->assertJson([
            'id' => 1,
            'title' => 'tit',
            'description' => 'desc',
            'diseases' => [],
            'status' => 'disabled',
            'type' => 'doctor',
        ]);
    }

    public function testUpdateService(): void
    {
        $service = Service::factory()->create([
            DiagnosticService::FIELD_CREATED_BY => $this->getLoggedUser()->getKey()
        ]);

        /** @var DoctorAccident $doctorAccident */
        $doctorAccident = DoctorAccident::factory()->create([
            DoctorAccidentService::FIELD_DOCTOR_ID => $this->getCurrentDoctor()->getKey(),
        ]);
        $doctorAccident->services()->attach($service);

        /** @var Accident $accident */
        $accident = $this->createAccidentForDoc();
        $accident->setAttribute(AccidentService::FIELD_CASEABLE_ID, $doctorAccident->getKey());
        $accident->save();

        $response = $this->sendPost('/api/doctor/accidents/'.$accident->id.'/services', [
            'id' => $service->getKey(),
            'title' => 'tit',
            'description' => 'desc',
            'status' => 'disabled',
            'type' => 'director',
        ]);
        $response->assertStatus(202);
        $response->assertJson([
            'id' => 1,
            'title' => 'tit',
            'description' => 'desc',
            'diseases' => [],
            'status' => 'disabled',
            'type' => 'doctor',
        ]);
        $services = $doctorAccident->services;
        $this->assertCount(1, $services);
        $this->assertSame($service->getKey(), $services[0]->getKey());
    }
}
