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
use medcenter24\mcCore\App\Services\Entity\DiagnosticService;
use medcenter24\mcCore\App\Services\Entity\DoctorAccidentService;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\App\Services\Entity\PatientService;
use medcenter24\mcCore\App\Services\Entity\PaymentService;
use medcenter24\mcCore\App\Services\Entity\ServiceService;
use medcenter24\mcCore\App\Services\Entity\SurveyService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class SearcherWithAllFiltersTest extends TestCase
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
  "result": "json",
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
    "assistants": [
      {
        "id": 1,
        "title": "Assistant1",
        "email": "a1@e.com",
        "comment": "",
        "refKey": "a1"
      },
      {
        "id": 2,
        "title": "Assistant2",
        "email": "a2@e.com",
        "comment": "",
        "refKey": "A2"
      }
    ],
    "doctors": [
      {
        "id": 1,
        "name": "a",
        "description": "c",
        "refKey": "b",
        "userId": "0",
        "medicalBoardNumber": "d"
      }
    ],
    "caseableTypes": [
      "doctor",
      "hospital"
    ],
    "patients": [
      {
        "id": 1,
        "name": "TEST-TESTOV",
        "address": "asdf",
        "phones": "123",
        "birthday": "1212-12-12",
        "comment": "asdf"
      }
    ],
    "handlingTimeRanges": [
      "2022-04-19>2022-04-24"
    ],
    "accidentStatuses": [
      {
        "id": 1,
        "title": "new",
        "type": "accident"
      },
      {
        "id": 2,
        "title": "assigned",
        "type": "doctor"
      },
      {
        "id": 3,
        "title": "in_progress",
        "type": "doctor"
      },
      {
        "id": 4,
        "title": "sent",
        "type": "doctor"
      },
      {
        "id": 5,
        "title": "paid",
        "type": "doctor"
      }
    ],
    "accidentTypes": [
      {
        "id": 1,
        "title": "insurance",
        "description": ""
      },
      {
        "id": 2,
        "title": "non-insurance",
        "description": ""
      }
    ],
    "visitTimeRanges": [
      "2022-04-18>2022-04-22"
    ],
    "doctorServices": [
      {
        "id": 1,
        "title": "service1",
        "description": "",
        "status": "active",
        "diseases": [],
        "type": "director"
      }
    ],
    "doctorSurveys": [
      {
        "id": 1,
        "title": "Survey1",
        "description": "",
        "status": "active",
        "diseases": [],
        "type": "director"
      }
    ],
    "doctorDiagnostics": [
      {
        "id": 1,
        "title": "diagnostic 1",
        "description": "",
        "diagnosticCategoryId": 0,
        "status": "active",
        "diseases": [],
        "type": "director"
      }
    ]
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
        /** @var DoctorAccident $doctorAccident */
        $doctorAccident = $doctorAccidentService->create([
            'visit_time' => '2022-04-19 13:04:05',
            'doctor_id'  => $doctor->getAttribute('id'),
        ]);

        $serviceService = $this->getServiceLocator()->get(ServiceService::class);
        $service = $serviceService->create([
            ServiceService::FIELD_TITLE => 'ser1',
        ]);
        $doctorAccident->services()->attach($service);

        $surveyService = $this->getServiceLocator()->get(SurveyService::class);
        $survey = $surveyService->create([
            SurveyService::FIELD_TITLE => 'sur1',
        ]);
        $doctorAccident->surveys()->attach($survey);

        $diagnosticService = $this->getServiceLocator()->get(DiagnosticService::class);
        $diagnostic = $diagnosticService->create([
            DiagnosticService::FIELD_TITLE => 'diag1',
        ]);
        $doctorAccident->diagnostics()->attach($diagnostic);

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
