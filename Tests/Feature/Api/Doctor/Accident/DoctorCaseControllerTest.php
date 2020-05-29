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
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Services\Entity\DiagnosticService;
use medcenter24\mcCore\App\Services\Entity\ServiceService;
use medcenter24\mcCore\App\Services\Entity\SurveyService;
use medcenter24\mcCore\Tests\TestCase;

class DoctorCaseControllerTest extends TestCase
{
    use TestDoctorAccidentTrait;

    public function testIndex(): void
    {
        // not included - hospital
        factory(Accident::class)->create([
            AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
        ]);
        // not included - wrong doctor
        factory(Accident::class)->create([
            AccidentService::FIELD_CASEABLE_TYPE => DoctorAccident::class,
        ]);

        // included to the result
        $this->createAccidentForDoc();

        $response = $this->sendGet('/api/doctor/accidents');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'id' => 3,
                    'assistantId' => 3,
                    'repeated' => 0,
                    'statusTitle' => 'assigned',
                    'price' => 0,
                    'doctorsFee' => 0,
                    'caseType' => 'doctor',
                    ]
            ],
            'meta' =>
                array (
                    'pagination' =>
                        array (
                            'total' => 1,
                            'count' => 1,
                            'per_page' => 10,
                            'current_page' => 1,
                            'total_pages' => 1,
                            'links' =>
                                array (
                                ),
                        ),
                ),
        ]);
    }

    public function testShowNoAccident(): void
    {
        $response = $this->sendGet('/api/doctor/accidents/0');
        $response->assertStatus(404);
    }

    public function testShowNoDoctorAccident(): void
    {
        $accident = factory(Accident::class)->create([
            AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
        ]);
        $response = $this->sendGet('/api/doctor/accidents/' . $accident->getKey());
        $response->assertStatus(404);
    }

    public function testShowAnotherDoctorAccident(): void
    {
        $accident = factory(Accident::class)->create([
            AccidentService::FIELD_CASEABLE_TYPE => DoctorAccident::class,
        ]);
        $response = $this->sendGet('/api/doctor/accidents/' . $accident->getKey());
        $response->assertStatus(404);
    }

    public function testShow(): void
    {
        /** @var Accident $accident */
        $accident = $this->createAccidentForDoc();
        $response = $this->sendGet('/api/doctor/accidents/' . $accident->getKey());
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'createdBy' => 2,
                'parentId' => 0,
                'patientId' => 1,
                'accidentTypeId' => 1,
                'assistantId' => 1,
                'caseableId' => 1,
                'cityId' => 2,
                'formReportId' => 1,
                'assistantPaymentId' => 0,
                'incomePaymentId' => 0,
                'assistantInvoiceId' => 0,
                'assistantGuaranteeId' => 0,
                'caseablePaymentId' => 0,
                'deletedAt' => '',
                'doctorId' => 1,
            ]
        ]);
        $accident->refresh();
        $status = $accident->getAttribute('accidentStatus');
        $this->assertSame(AccidentStatusService::STATUS_IN_PROGRESS, $status->getAttribute(AccidentStatusService::FIELD_TITLE));
    }

    public function testUpdate(): void
    {
        /** @var ServiceService $accidentServiceService */
        $accidentServiceService = $this->getServiceLocator()->get(ServiceService::class);

        /** @var DiagnosticService $diagnosticService */
        $diagnosticService = $this->getServiceLocator()->get(DiagnosticService::class);

        /** @var SurveyService $surveyService */
        $surveyService = $this->getServiceLocator()->get(SurveyService::class);

        /** @var Accident $accident */
        $accident = $this->createAccidentForDoc();
        $response = $this->sendPut('/api/doctor/accidents/' . $accident->getKey(), [
            'diagnose' => 'Recommendation',
            'investigation' => 'Investigation',
            'visitDateTime' => '2017-01-20 01:30:54',
            'diagnostics' => [
                $diagnosticService->create()->getKey(),
                $diagnosticService->create()->getKey(),
            ],
            'services' => [
                $accidentServiceService->create()->getKey(),
                $accidentServiceService->create()->getKey(),
            ],
            'surveys' => [
                $surveyService->create()->getKey(),
                $surveyService->create()->getKey(),
            ],
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'createdBy' => 2,
                'parentId' => 0,
                'patientId' => 1,
                'accidentTypeId' => 1,
                'accidentStatusId' => 1,
                'assistantId' => 1,
                'caseableId' => 1,
                'cityId' => 2,
                'formReportId' => 1,
                'caseableType' => 'doctor',
                'assistantPaymentId' => 0,
                'incomePaymentId' => 0,
                'assistantInvoiceId' => 0,
                'assistantGuaranteeId' => 0,
                'caseablePaymentId' => 0,
                'deletedAt' => '',
                'closedAt' => '',
                'recommendation' => 'Recommendation',
                'investigation' => 'Investigation',
                'doctorId' => 1,
                'visitTime' => '2017-01-20 01:30:54',
            ]
        ]);
        
        $accident->refresh();

        /** @var AccidentService $accidentService */
        $accidentService = $this->getServiceLocator()->get(AccidentService::class);
        $this->assertSame([
            [
                'id' => 1,
                'title' => '',
                'description' => '',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'title' => '',
                'description' => '',
                'status' => 'active',
            ],
        ], $accidentService->getAccidentServices($accident)->toArray());

        $this->assertSame([
            [
                'id' => 1,
                'created_by' => '0',
                'title' => '',
                'description' => '',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'created_by' => '0',
                'title' => '',
                'description' => '',
                'status' => 'active',
            ],
        ], $accident->caseable->surveys->toArray());

        $this->assertSame([
            [
                'id' => 1,
                'created_by' => '0',
                'diagnostic_category_id' => '0',
                'title' => '',
                'status' => 'active',
                'description' => '',
            ],
            [
                'id' => 2,
                'created_by' => '0',
                'diagnostic_category_id' => '0',
                'title' => '',
                'status' => 'active',
                'description' => '',
            ],
        ], $accident->caseable->diagnostics->toArray());
    }
}
