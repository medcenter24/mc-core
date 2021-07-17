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

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases\CaseController;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\App\Services\Entity\DiagnosticService;
use medcenter24\mcCore\App\Services\Entity\ServiceService;
use medcenter24\mcCore\App\Services\Entity\SurveyService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class CasesControllerUpdateActionTest extends TestCase
{
    use DirectorTestTraitApi;

    private const API_URL = '/api/director/cases/';

    private ?AccidentService $accidentService = null;
    private ?SurveyService $surveyService = null;
    private ?ServiceService $serviceService = null;
    private ?DiagnosticService $diagnosticService = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accidentService = new AccidentService();
        $this->serviceService = new ServiceService();
        $this->diagnosticService = new DiagnosticService();
        $this->surveyService = new SurveyService();
    }

    public function testUpdateWithoutData(): void
    {
        $accident = $this->getServiceLocator()->get(CaseAccidentService::class)->create();

        $this->doNotPrintErrResponse([422]);
        $response = $this->sendPut(self::API_URL.$accident->id, []);
        $this->doNotPrintErrResponse();
        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    ['Accident identifier should be provided in the request data'],
                ],
            ]);
    }

    /**
     * Creating a case but with the data for accident only (without dependencies and relations)
     */
    public function testUpdateWithAnotherAccident(): void
    {
        $accident = Accident::factory()->create([
            'accident_status_id' => AccidentStatus::factory()->create([
                'title' => 'anything1'
            ])->id,
        ]);
        $accident2 = Accident::factory()->create([
            'accident_status_id' => AccidentStatus::factory()->create([
                'title' => 'anything2'
            ])->id,
        ]);
        $data = [
            'accident' => [
                'id' => $accident2->id,
            ]
        ];

        $this->doNotPrintErrResponse([422]);
        $response = $this->sendPut(self::API_URL.$accident->id, $data);
        $this->doNotPrintErrResponse();

        $response->assertStatus(422)->assertJson([
            'errors' => [
                ['There are 2 different accidents in the request'],
            ],
        ]);
    }

    /**
     * Check that if I sent incorrect data to the relation it won't be saved
     */
    public function testUpdateWithNonExistingRelations(): void
    {
        $accident = Accident::factory()->create([
            'accident_status_id' => AccidentStatus::factory()->create([
                'title' => 'anything'
            ])->id,
        ]);
        $data = [
            'accident' => [
                'accidentStatusId' => 100,
                'accidentTypeId' => 100,
                'assistantId' => 100,
                'caseableId' => 100,
                'cityId' => 100,
                'formReportId' => 100,
                'id' => $accident->id,
                'parentId' => 100,
                'patientId' => 100,
                'assistantPaymentId' => 100,
                'assistantGuaranteeId' => 100,
                'incomePaymentId' => 100,
                'caseablePaymentId' => 100,
            ]
        ];

        $this->doNotPrintErrResponse([422]);
        $response = $this->sendPut(self::API_URL.$accident->id, $data);
        $this->doNotPrintErrResponse();

        $content = $response->assertStatus(422)->getContent();
        $ans = json_decode($content);
        self::assertJson($ans->errors->accident[0]);
        self::assertSame([
            'parentId' => [
                'Parent is incorrect',
            ],
            'patientId' => [
                'Is not exists'
            ],
            'accidentTypeId' => [
                'Is not exists'
            ],
            'accidentStatusId' => [
                'Is not exists'
            ],
            'assistantId' => [
                'Is not exists'
            ],
            'assistantGuaranteeId' => [
                'Is not exists',
            ],
            'formReportId' => [
                'Is not exists'
            ],
            'cityId' => [
                'Is not exists'
            ],
            'caseablePaymentId' => [
                'Is not exists'
            ],
            'incomePaymentId' => [
                'Is not exists'
            ],
            'assistantPaymentId' => [
                'Is not exists'
            ],
        ], json_decode($ans->errors->accident[0], true));
    }

    /**
     * @throws InconsistentDataException
     */
    public function testClosedAccident(): void
    {
        $accident = Accident::factory()->create(['accident_status_id' => 0]);
        $data = [
            'accident' => [
                'id' => $accident->id,
            ]
        ];

        $accidentService = new AccidentService();
        // closing an accident
        $accidentService->closeAccident($accident);

        $this->doNotPrintErrResponse([422]);
        $response = $this->sendPut(self::API_URL.$accident->id, $data);
        $this->doNotPrintErrResponse();

        $response->assertStatus(422)->assertJson([
            'message' => '422 Unprocessable Entity',
            'errors' => [
                ['Accident closed and can not be changed']
            ],
        ]);
    }

    public function testDeletedAccident(): void
    {
        /** @var Accident $accident */
        $accident = Accident::factory()->create([
            'accident_status_id' => AccidentStatus::factory()->create([
                'title' => 'anything'
            ])->id,
        ]);
        $accident->delete();
        $data = [
            'accident' => [
                'id' => $accident->id,
            ]
        ];

        $this->doNotPrintErrResponse([422]);
        $response = $this->sendPut(self::API_URL.$accident->id, $data);
        $this->doNotPrintErrResponse();

        $response->assertStatus(422)->assertJson([
            'message' => '422 Unprocessable Entity',
            'errors' => [
                ['Accident not found']
            ],
        ]);
    }

    public function testChangeDoctorAccident(): void
    {
        /** @var Accident $accident */
        $accident = Accident::factory()->create([
            'accident_status_id' => AccidentStatus::factory()->create([
                'title' => 'anything'
            ])->id,
            'caseable_id' => $caseableId = DoctorAccident::factory()->create([
                'doctor_id' => $doc1 = Doctor::factory()->create()->id
            ])->id,
        ]);
        
        $data = [
            'accident' => [
                'id' => $accident->id,
            ],
            'doctorAccident' => [
                'id' => $caseableId,
                'doctorId' => $doc2 = Doctor::factory()->create()->id // new doc
            ],
        ];

        $this->assertNotSame($doc1, $doc2);
        
        $response = $this->sendPut(self::API_URL.$accident->id, $data);

        $response->assertStatus(202);

        $accident->refresh();
        $this->assertSame($doc2, (int) $accident->caseable->doctor_id);
    }

    public function testCaseableMorphs(): void
    {
        /** @var Accident $accident */
        $accident = $this->accidentService->create();
        $response = $this->sendPut(self::API_URL . $accident->id, [
            CaseAccidentService::PROPERTY_ACCIDENT => [
                AccidentService::FIELD_ID => $accident->id,
            ],
            CaseAccidentService::PROPERTY_SERVICES => [
                $this->serviceService->create()->id,
                $this->serviceService->create()->id,
                $this->serviceService->create()->id,
            ],
            CaseAccidentService::PROPERTY_DIAGNOSTICS => [
                $this->diagnosticService->create()->id,
                $this->diagnosticService->create()->id,
                $this->diagnosticService->create()->id,
            ],
            CaseAccidentService::PROPERTY_SURVEYS => [
                $this->surveyService->create()->id,
                $this->surveyService->create()->id,
                $this->surveyService->create()->id,
            ]
        ]);

        $response->assertStatus(202);
        $accident->refresh();
        $services = $this->accidentService->getAccidentServices($accident);
        $this->assertCount(3, $services);
        $this->assertCount(3, $accident->caseable->diagnostics);
        $this->assertCount(3, $accident->caseable->surveys);
    }
}
