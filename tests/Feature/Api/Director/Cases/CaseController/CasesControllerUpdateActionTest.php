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
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class CasesControllerUpdateActionTest extends TestCase
{
    use DirectorTestTraitApi;

    public function testUpdateWithoutData(): void
    {
        $accident = $this->getServiceLocator()->get(CaseAccidentService::class)->create();

        $this->doNotPrintErrResponse([422]);
        $response = $this->sendPut('/api/director/cases/'.$accident->id, []);
        $this->doNotPrintErrResponse();
        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    ['Accident data should be provided in the request data'],
                ],
            ]);
    }

    /**
     * Creating a case but with the data for accident only (without dependencies and relations)
     */
    public function testUpdateWithAnotherAccident(): void
    {
        $accident = factory(Accident::class)->create([
            'accident_status_id' => factory(AccidentStatus::class)->create([
                'title' => 'anything1'
            ])->id,
        ]);
        $accident2 = factory(Accident::class)->create([
            'accident_status_id' => factory(AccidentStatus::class)->create([
                'title' => 'anything2'
            ])->id,
        ]);
        $data = [
            'accident' => [
                'id' => $accident2->id,
            ]
        ];

        $this->doNotPrintErrResponse([422]);
        $response = $this->sendPut('/api/director/cases/'.$accident->id, $data);
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
        $accident = factory(Accident::class)->create([
            'accident_status_id' => factory(AccidentStatus::class)->create([
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
        $response = $this->sendPut('/api/director/cases/'.$accident->id, $data);
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

    public function testClosedAccident(): void
    {
        $accident = factory(Accident::class)->create(['accident_status_id' => 0]);
        $data = [
            'accident' => [
                'id' => $accident->id,
            ]
        ];

        $accidentService = new AccidentService();
        // closing an accident
        $accidentService->closeAccident($accident);

        $this->doNotPrintErrResponse([422]);
        $response = $this->sendPut('/api/director/cases/'.$accident->id, $data);
        $this->doNotPrintErrResponse();

        $response->assertStatus(422)->assertJson([
            'message' => '422 Unprocessable Entity',
            'errors' => [
                ['Already closed']
            ],
        ]);
    }

    public function testDeletedAccident(): void
    {
        /** @var Accident $accident */
        $accident = factory(Accident::class)->create([
            'accident_status_id' => factory(AccidentStatus::class)->create([
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
        $response = $this->sendPut('/api/director/cases/'.$accident->id, $data);
        $this->doNotPrintErrResponse();

        $response->assertStatus(422)->assertJson([
            'message' => '422 Unprocessable Entity',
            'errors' => [
                ['Accident not found']
            ],
        ]);
    }
}
