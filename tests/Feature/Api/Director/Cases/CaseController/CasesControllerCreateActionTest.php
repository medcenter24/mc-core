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
use medcenter24\mcCore\App\Entity\AccidentType;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\FormReport;
use medcenter24\mcCore\App\Entity\Patient;
use medcenter24\mcCore\App\Entity\Payment;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Transformers\CaseAccidentTransformer;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class CasesControllerCreateActionTest extends TestCase
{

    use DirectorTestTraitApi;

    public function testCreateWithoutData(): void
    {
        $response = $this->sendPost('/api/director/cases', []);
        $response->assertStatus(201)->assertJson([
            'id' => 1,
            'assistantId' => 0,
            'repeated' => 0,
            'assistantRefNum' => '',
            'symptoms' => '',
            'handlingTime' => '',
            'patientName' => '',
            'checkpoints' => '',
            'statusTitle' => 'new',
            'cityTitle' => '',
            'price' => 0,
            'doctorsFee' => 0,
            'caseType' => 'doctor',
        ]);
        $refNum = $response->json('refNum');
        $this->assertStringStartsWith('NA0001-', $refNum);
    }

    /**
     * Creating a case but with the data for accident only (without dependencies and relations)
     */
    public function testCreateWithAccidentDataOnlyEmptyAllData(): void
    {
        $data = [
            'accident' => [
                'accidentStatusId' => '',
                'accidentTypeId' => '',
                'address' => '',
                'assistantId' => '',
                'assistantRefNum' => '',
                'caseableId' => '',
                'caseableType' => '',
                'cityId' => '',
                'closedAt' => '',
                'contacts' => '',
                'createdAt' => '',
                'createdBy' => '',
                'deletedAt' => '',
                'formReportId' => '',
                'handlingTime' => '',
                'id' => '',
                'income' => '',
                'parentId' => '',
                'patientId' => '',
                'refNum' => '',
                'symptoms' => '',
                'title' => '',
                'updatedAt' => '',
                'assistantPaymentId' => NULL,
                'incomePaymentId' => NULL,
                'caseablePaymentId' => NULL,
            ]
        ];
        $response = $this->sendPost('/api/director/cases', $data);

        $response->assertStatus(201)->assertJson([
            'id' => 1,
            'assistantId' => 0,
            'repeated' => 0,
            'assistantRefNum' => '',
            'symptoms' => '',
            'handlingTime' => '',
            'patientName' => '',
            'checkpoints' => '',
            'statusTitle' => 'new',
            'cityTitle' => '',
            'price' => 0,
            'doctorsFee' => 0,
            'caseType' => 'doctor',
        ]);
    }

    /**
     * Creating a case but with the data for accident only (without dependencies and relations)
     */
    public function testCreateWithAccidentDataOnlyAccidentRequestRules(): void
    {
        $data = [
            'accident' => [
                'accidentStatusId' => '1',
                'accidentTypeId' => '1',
                'address' => '',
                'assistantId' => 2,
                'assistantRefNum' => '',
                'caseableId' => '3',
                'caseableType' => 'doctor',
                'cityId' => 3,
                'closedAt' => null,
                'contacts' => '',
                'createdBy' => '2',
                'deletedAt' => null,
                'formReportId' => '7',
                'handlingTime' => '2018-11-16 02:46:00',
                'id' => 4,
                'income' => null,
                'parentId' => 3,
                'patientId' => 3,
                'refNum' => 'test',
                'symptoms' => '',
                'title' => '',
                'assistantPaymentId' => 2,
                'incomePaymentId' => 5,
                'caseablePaymentId' => 8,
            ]
        ];

        $this->doNotPrintErrResponse([422]);
        $response = $this->sendPost('/api/director/cases', $data);
        $this->doNotPrintErrResponse();
        $content = $response->assertStatus(422)->getContent();
        $ans = json_decode($content);
        self::assertJson($ans->errors->accident[0]);
        self::assertSame([
            'parentId' => [
                'Parent is incorrect',
            ],
            'caseableId' => [
                'Caseable does not exists',
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
     * Creating a case but with the data for accident only (without dependencies and relations)
     */
    public function testCreateWithAccidentDataOnly4(): void
    {
        $patient = factory(Patient::class)->create();
        $city = factory(City::class)->create();
        $assistantPayment = factory(Payment::class)->create();
        $caseablePayment = factory(Payment::class)->create();
        $income = factory(Payment::class)->create();
        $data = [
            'accident' => [
                'accidentStatusId' => AccidentStatus::firstOrCreate([
                    'title' => AccidentStatusService::STATUS_NEW,
                    'type' => AccidentStatusService::TYPE_ACCIDENT,
                ])->id,
                'accidentTypeId' => factory(AccidentType::class)->create()->id,
                'address' => 'any',
                'assistantId' => factory(Assistant::class)->create()->id,
                'assistantRefNum' => 'ref---',
                'caseableId' => factory(DoctorAccident::class)->create()->id,
                'caseableType' => CaseAccidentTransformer::CASE_TYPE_DOCTOR,
                'cityId' => $city->id,
                'closedAt' => null,
                'contacts' => 'anything',
                'deletedAt' => null,
                'formReportId' => factory(FormReport::class)->create()->id,
                'handlingTime' => '2018-11-16 02:46:00',
                'parentId' => factory(Accident::class)->create()->id,
                'patientId' => $patient->id,
                'refNum' => 'test',
                'symptoms' => 'aaa',
                'title' => 'ccc',
                'assistantPaymentId' => $assistantPayment->id,
                'incomePaymentId' => $income->id,
                'caseablePaymentId' => $caseablePayment->id,
            ]
        ];

        $response = $this->sendPost('/api/director/cases', $data);

        $response->assertStatus(201)
            ->assertJson([
            'id' => 2,
            'assistantId' => 1,
            'repeated' => 1,
            'assistantRefNum' => 'ref---',
            'symptoms' => 'aaa',
            'handlingTime' => '2018-11-16 02:46:00',
            'patientName' => $patient->name,
            'checkpoints' => '',
            'statusTitle' => 'new',
            'cityTitle' => $city->title,
            'price' => $income->value,
            'doctorsFee' => $caseablePayment->value,
            'caseType' => 'doctor',
        ]);
    }

    public function testCreateWithAccidentDataOnly5(): void
    {
        $patient = factory(Patient::class)->create();
        $data = [
            'accident' => [
                'accidentStatusId' => (new AccidentStatusService())->getNewStatus()->getAttribute('id'),
                'accidentTypeId' => factory(AccidentType::class)->create()->id,
                'address' => 'any',
                'assistantId' => factory(Assistant::class)->create()->id,
                'assistantRefNum' => 'ref---',
                'caseableId' => factory(DoctorAccident::class)->create()->id,
                'caseableType' => 'doctor',
                'cityId' => factory(City::class)->create()->id,
                'closedAt' => null,
                'contacts' => 'anything',
                'deletedAt' => null,
                'formReportId' => factory(FormReport::class)->create()->id,
                'handlingTime' => '2018-11-16 02:46:00',
                'parentId' => factory(Accident::class)->create()->id,
                'patientId' => $patient->id,
                'refNum' => 'test',
                'symptoms' => 'aaa',
                'title' => 'ccc',
                'assistantPaymentId' => factory(Payment::class)->create()->id,
                'incomePaymentId' => factory(Payment::class)->create()->id,
                'caseablePaymentId' => factory(Payment::class)->create()->id,
            ]
        ];
        $response = $this->sendPost('/api/director/cases', $data);

        $response->assertStatus(201)->assertJson([
            'id' => 2,
            'assistantId' => 1,
            'repeated' => 1,
            'refNum' => 'test',
            'assistantRefNum' => 'ref---',
            'symptoms' => 'aaa',
            'handlingTime' => '2018-11-16 02:46:00',
            'patientName' => $patient->name,
            'checkpoints' => '',
            'statusTitle' => 'new',
            'caseType' => 'doctor',
        ]);
    }
}
