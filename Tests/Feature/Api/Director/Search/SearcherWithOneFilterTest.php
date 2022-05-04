<?php
/*
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
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace Api\Director\Search;

use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Services\Entity\AccidentTypeService;
use medcenter24\mcCore\App\Services\Entity\AssistantService;
use medcenter24\mcCore\App\Services\Entity\CityService;
use medcenter24\mcCore\App\Services\Entity\DoctorAccidentService;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\App\Services\Entity\PatientService;
use medcenter24\mcCore\App\Services\Entity\PaymentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class SearcherWithOneFilterTest extends TestCase
{
    use DirectorTestTraitApi;

    public function testSearch(): void
    {
        $this->createMockedSearchableModels();
        $response = $this->sendPost('/api/director/search/search', json_decode($this->getPayload(), true));
        $response->assertStatus(200);
        $response->assertExactJson([
            [
                "assist-ref-num" => "PhpUnitRefNumAssistant",
                "city" => "City",
                "doctor-income" => 5.11,
                "npp" => 1,
                "patient" => "TEST-TESTOV",
            ],
        ]);
    }

    public function getPayload(): string
    {
        return '{
  "id": 20,
  "title": "2022-04-22 00:08:22",
  "type": "searcher",
  "result": "datatable",
  "filters": {
    "cities": [
      {
        "id": 1,
        "title": "City",
        "regionId": 1,
        "regionTitle": "Region",
        "countryTitle": "Country"
      }
    ],
    "assistants": [],
    "doctors": [],
    "caseableTypes": [],
    "patients": [],
    "handlingTimeRanges": [],
    "accidentStatuses": [],
    "accidentTypes": [],
    "visitTimeRanges": [],
    "doctorServices": [],
    "doctorSurveys": [],
    "doctorDiagnostics": []
  },
  "fields": [
    {
      "id": "npp",
      "title": "Npp",
      "order": "desc",
      "sort": 1
    },
    {
      "id": "patient",
      "title": "Patient",
      "order": "asc",
      "sort": 2
    },
    {
      "id": "city",
      "title": "City",
      "order": "asc",
      "sort": 3
    },
    {
      "id": "doctor-income",
      "title": "Doctor\'s fees",
      "order": "",
      "sort": 4
    },
    {
      "id": "assist-ref-num",
      "title": "Assistant Ref. Number",
      "order": "asc",
      "sort": 5
    }
  ]
}';
    }

    private function createMockedSearchableModels(): void
    {
        /** @var CityService $cityService */
        $cityService = $this->getServiceLocator()->get(CityService::class);
        $city = $cityService->create([
            CityService::FIELD_TITLE => 'City',
        ]);

        /** @var AssistantService $assistanceService */
        $assistanceService = $this->getServiceLocator()->get(AssistantService::class);
        $assistant = $assistanceService->create([
            AssistantService::FIELD_TITLE => 'Assistant1',
        ]);

        /** @var DoctorService $doctorService */
        $doctorService = $this->getServiceLocator()->get(DoctorService::class);
        $doctor = $doctorService->create([
            'name' => 'a',
        ]);

        /** @var PatientService $patientService */
        $patientService = $this->getServiceLocator()->get(PatientService::class);
        $patient = $patientService->create([
            'name' => 'TEST-TESTOV',
        ]);

        /** @var AccidentStatusService $accidentStatusService */
        $accidentStatusService = $this->getServiceLocator()->get(AccidentStatusService::class);
        $accidentStatus = $accidentStatusService->create([
            'title' => 'new',
        ]);

        /** @var AccidentTypeService $accidentTypeService */
        $accidentTypeService = $this->getServiceLocator()->get(AccidentTypeService::class);
        $accidentType = $accidentTypeService->create([
            'title' => 'insurance',
        ]);

        /** @var DoctorAccidentService $doctorAccidentService */
        $doctorAccidentService = $this->getServiceLocator()->get(DoctorAccidentService::class);
        $doctorAccident = $doctorAccidentService->create([
            'visit_time' => '2022-04-19 13:04:05',
            'doctor_id'  => $doctor->getAttribute('id'),
        ]);

        /** @var PaymentService $paymentService */
        $paymentService = $this->getServiceLocator()->get(PaymentService::class);
        $payment = $paymentService->create([
            'value' => 5.11,
        ]);

        /** @var AccidentService $accidentService */
        $accidentService = $this->getServiceLocator()->get(AccidentService::class);
        $accidentService->create([
            'caseable_type' => DoctorAccident::class,
            'handling_time' => '2022-04-21 17:18:10',
            'city_id' => $city->getAttribute('id'),
            'patient_id' => $patient->getAttribute('id'),
            'caseable_payment_id' => $payment->getAttribute('id'),
            'assistant_id' => $assistant->getAttribute('id'),
            'caseable_id' => $doctorAccident->getAttribute('id'),
            'accident_status_id' => $accidentStatus->getAttribute('id'),
            'accident_type_id' => $accidentType->getAttribute('id'),
            'assistant_ref_num' => 'PhpUnitRefNumAssistant',
        ]);
    }
}
