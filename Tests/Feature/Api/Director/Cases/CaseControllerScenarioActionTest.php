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

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Hospital;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Entity\Invoice;
use medcenter24\mcCore\App\Entity\Payment;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Entity\Upload;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;
use ScenariosTableSeeder;

class CaseControllerScenarioActionTest extends TestCase
{
    use DirectorTestTraitApi;

    private CaseAccidentService $caseAccidentService;

    public function setUp(): void
    {
        parent::setUp();
        // adding scenarios to the storage
        (new ScenariosTableSeeder())->run();
        $this->caseAccidentService = new CaseAccidentService();
    }

    /**
     * @throws InconsistentDataException
     */
    public function testGetDefaultScenario(): void
    {
        $accident = $this->caseAccidentService->create();

        $response = $this->sendGet('/api/director/cases/' . $accident->getKey() . '/scenario');
        $response->assertJson([
            'data' =>
                [
                    [
                        'id' => 1,
                        'tag' => 'doctor',
                        'order' => 1,
                        'mode' => 'step',
                        'accidentStatusId' => 1,
                        'status' => 'current',
                        'title' => AccidentStatusService::STATUS_NEW,
                    ],
                    [
                        'id' => 2,
                        'tag' => 'doctor',
                        'order' => 2,
                        'mode' => 'step',
                        'accidentStatusId' => 2,
                        'status' => '',
                        'title' => AccidentStatusService::STATUS_ASSIGNED,
                    ],
                    [
                        'id' => 3,
                        'tag' => 'doctor',
                        'order' => 3,
                        'mode' => 'step',
                        'accidentStatusId' => 3,
                        'status' => '',
                        'title' => AccidentStatusService::STATUS_IN_PROGRESS,
                    ],
                    [
                        'id' => 4,
                        'tag' => 'doctor',
                        'order' => 4,
                        'mode' => 'step',
                        'accidentStatusId' => 4,
                        'status' => '',
                        'title' => AccidentStatusService::STATUS_SENT,
                    ],
                    [
                        'id' => 5,
                        'tag' => 'doctor',
                        'order' => 5,
                        'mode' => 'step',
                        'accidentStatusId' => 5,
                        'status' => '',
                        'title' => AccidentStatusService::STATUS_PAID,
                    ],
                    [
                        'id' => 7,
                        'tag' => 'doctor',
                        'order' => 7,
                        'mode' => 'step',
                        'accidentStatusId' => 7,
                        'status' => '',
                        'title' => AccidentStatusService::STATUS_CLOSED,
                    ],
                ],
            ]);
    }

    public function testHospitalCaseScenarioCurrentNew(): void
    {
        $accident = $this->caseAccidentService->create([
            CaseAccidentService::PROPERTY_ACCIDENT => [
                AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
            ]
        ]);

        $accidentId = $accident->getKey();

        $response2 = $this->sendGet('/api/director/cases/' . $accidentId . '/scenario');
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 8,
                        'tag' => 'hospital',
                        'order' => 1,
                        'mode' => 'step',
                        'accidentStatusId' => 1,
                        'status' => 'current',
                        'title' => 'new',
                    ],
                    [
                        'id' => 9,
                        'tag' => 'hospital',
                        'order' => '2',
                        'mode' => 'step',
                        'accidentStatusId' => '8',
                        'status' => '',
                        'title' => 'assistant_guarantee',
                    ],
                    [
                        'id' => 10,
                        'tag' => 'hospital',
                        'order' => '3',
                        'mode' => 'step',
                        'accidentStatusId' => '9',
                        'status' => '',
                        'title' => 'assigned',
                    ],
                    [
                        'id' => 11,
                        'tag' => 'hospital',
                        'order' => '4',
                        'mode' => 'step',
                        'accidentStatusId' => '10',
                        'status' => '',
                        'title' => 'hospital_guarantee',
                    ],
                    [
                        'id' => 12,
                        'tag' => 'hospital',
                        'order' => '5',
                        'mode' => 'step',
                        'accidentStatusId' => '11',
                        'status' => '',
                        'title' => 'hospital_invoice',
                    ],
                    [
                        'id' => 13,
                        'tag' => 'hospital',
                        'order' => '6',
                        'mode' => 'step',
                        'accidentStatusId' => '12',
                        'status' => '',
                        'title' => 'assistant_invoice',
                    ],
                    [
                        'id' => 14,
                        'tag' => 'hospital',
                        'order' => '7',
                        'mode' => 'step',
                        'accidentStatusId' => '13',
                        'status' => '',
                        'title' => 'paid',
                    ],
                    [
                        'id' => 15,
                        'tag' => 'hospital',
                        'order' => '8',
                        'mode' => 'step',
                        'accidentStatusId' => '7',
                        'status' => '',
                        'title' => 'closed',
                    ],
                ],
        ]);
    }

    public function testHospitalCaseScenarioPassAllSteps(): void
    {
        $accident = factory(Accident::class)->create([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => factory(HospitalAccident::class)->create([
                'hospital_id' => 0,
                'hospital_guarantee_id' => 0,
                'hospital_invoice_id' => 0,
            ])->id,
            'assistant_invoice_id' => factory(Invoice::class)->create()->id,
            'assistant_payment_id' => factory(Payment::class)->create()->id,
            'assistant_guarantee_id' => factory(Upload::class)->create()->id,
        ]);

        $accident->caseable->fill([
            'hospital_id' => factory(Hospital::class)->create()->id,
            'hospital_guarantee_id' => factory(Upload::class)->create()->id,
            'hospital_invoice_id' => factory(Invoice::class)->create()->id,
        ])->save();

        (new AccidentService())->closeAccident($accident);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 8,
                        'tag' => 'hospital',
                        'order' => '1',
                        'mode' => 'step',
                        'accidentStatusId' => '1',
                        'status' => 'visited',
                        'title' => 'new',
                    ],
                    [
                        'id' => 9,
                        'tag' => 'hospital',
                        'order' => '2',
                        'mode' => 'step',
                        'accidentStatusId' => '8',
                        'status' => 'visited',
                        'title' => 'assistant_guarantee',
                    ],
                    [
                        'id' => 10,
                        'tag' => 'hospital',
                        'order' => '3',
                        'mode' => 'step',
                        'accidentStatusId' => '9',
                        'status' => 'visited',
                        'title' => 'assigned',
                    ],
                    [
                        'id' => 11,
                        'tag' => 'hospital',
                        'order' => '4',
                        'mode' => 'step',
                        'accidentStatusId' => '10',
                        'status' => 'visited',
                        'title' => 'hospital_guarantee',
                    ],
                    [
                        'id' => 12,
                        'tag' => 'hospital',
                        'order' => '5',
                        'mode' => 'step',
                        'accidentStatusId' => '11',
                        'status' => 'visited',
                        'title' => 'hospital_invoice',
                    ],
                    [
                        'id' => 13,
                        'tag' => 'hospital',
                        'order' => '6',
                        'mode' => 'step',
                        'accidentStatusId' => '12',
                        'status' => 'visited',
                        'title' => 'assistant_invoice',
                    ],
                    [
                        'id' => 14,
                        'tag' => 'hospital',
                        'order' => '7',
                        'mode' => 'step',
                        'accidentStatusId' => '13',
                        'status' => 'visited',
                        'title' => 'paid',
                    ],
                    [
                        'id' => 15,
                        'tag' => 'hospital',
                        'order' => '8',
                        'mode' => 'step',
                        'accidentStatusId' => '7',
                        'status' => 'current',
                        'title' => 'closed',
                    ],
                ],
        ]);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testHospitalCaseScenarioCreateAndClose(): void
    {
        $accident = factory(Accident::class)->create([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => 0,
            'assistant_invoice_id' => 0,
            'assistant_payment_id' => 0,
            'assistant_guarantee_id' => 0,
        ]);

        (new AccidentService())->closeAccident($accident);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertStatus(200);
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 8,
                        'tag' => 'hospital',
                        'order' => '1',
                        'mode' => 'step',
                        'accidentStatusId' => '1',
                        'status' => 'visited',
                        'title' => 'new',
                    ],
                    [
                        'id' => 9,
                        'tag' => 'hospital',
                        'order' => '2',
                        'mode' => 'step',
                        'accidentStatusId' => '8',
                        'status' => '',
                        'title' => 'assistant_guarantee',
                    ],
                    [
                        'id' => 10,
                        'tag' => 'hospital',
                        'order' => '3',
                        'mode' => 'step',
                        'accidentStatusId' => '9',
                        'status' => '',
                        'title' => 'assigned',
                    ],
                    [
                        'id' => 11,
                        'tag' => 'hospital',
                        'order' => '4',
                        'mode' => 'step',
                        'accidentStatusId' => '10',
                        'status' => '',
                        'title' => 'hospital_guarantee',
                    ],
                    [
                        'id' => 12,
                        'tag' => 'hospital',
                        'order' => '5',
                        'mode' => 'step',
                        'accidentStatusId' => '11',
                        'status' => '',
                        'title' => 'hospital_invoice',
                    ],
                    [
                        'id' => 13,
                        'tag' => 'hospital',
                        'order' => '6',
                        'mode' => 'step',
                        'accidentStatusId' => '12',
                        'status' => '',
                        'title' => 'assistant_invoice',
                    ],
                    [
                        'id' => 14,
                        'tag' => 'hospital',
                        'order' => '7',
                        'mode' => 'step',
                        'accidentStatusId' => '13',
                        'status' => '',
                        'title' => 'paid',
                    ],
                    [
                        'id' => 15,
                        'tag' => 'hospital',
                        'order' => '8',
                        'mode' => 'step',
                        'accidentStatusId' => '7',
                        'status' => 'current',
                        'title' => 'closed',
                    ],
                ],
        ]);
    }

    public function testHospitalCaseScenarioPartialSteps(): void
    {
        $accident = factory(Accident::class)->create([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => factory(HospitalAccident::class)->create([
                'hospital_id' => 0,
                'hospital_guarantee_id' => 0,
                'hospital_invoice_id' => 0,
            ])->id,
            'assistant_invoice_id' => 0,
            'assistant_payment_id' => factory(Payment::class)->create()->id,
            'assistant_guarantee_id' => 0,
            'accident_status_id' => (new AccidentStatusService())->getNewStatus(),
        ]);

        $accident->caseable->fill([
            'hospital_id' => factory(Hospital::class)->create()->id,
            'hospital_guarantee_id' => 0,
            'hospital_invoice_id' => 0,
        ])->save();

        (new AccidentService())->closeAccident($accident);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 8,
                        'tag' => 'hospital',
                        'order' => '1',
                        'mode' => 'step',
                        'accidentStatusId' => '1',
                        'status' => 'visited',
                        'title' => 'new',
                    ],
                    [
                        'id' => 9,
                        'tag' => 'hospital',
                        'order' => '2',
                        'mode' => 'step',
                        'accidentStatusId' => '8',
                        'status' => '',
                        'title' => 'assistant_guarantee',
                    ],
                    [
                        'id' => 10,
                        'tag' => 'hospital',
                        'order' => '3',
                        'mode' => 'step',
                        'accidentStatusId' => '9',
                        'status' => 'visited',
                        'title' => 'assigned',
                    ],
                    [
                        'id' => 11,
                        'tag' => 'hospital',
                        'order' => '4',
                        'mode' => 'step',
                        'accidentStatusId' => '10',
                        'status' => '',
                        'title' => 'hospital_guarantee',
                    ],
                    [
                        'id' => 12,
                        'tag' => 'hospital',
                        'order' => '5',
                        'mode' => 'step',
                        'accidentStatusId' => '11',
                        'status' => '',
                        'title' => 'hospital_invoice',
                    ],
                    [
                        'id' => 13,
                        'tag' => 'hospital',
                        'order' => '6',
                        'mode' => 'step',
                        'accidentStatusId' => '12',
                        'status' => '',
                        'title' => 'assistant_invoice',
                    ],
                    [
                        'id' => 14,
                        'tag' => 'hospital',
                        'order' => '7',
                        'mode' => 'step',
                        'accidentStatusId' => '13',
                        'status' => 'visited',
                        'title' => 'paid',
                    ],
                    [
                        'id' => 15,
                        'tag' => 'hospital',
                        'order' => '8',
                        'mode' => 'step',
                        'accidentStatusId' => '7',
                        'status' => 'current',
                        'title' => 'closed',
                    ],
                ],
        ]);
    }

    public function testDoctorCaseScenarioNew(): void
    {
        $accidentId = (new AccidentService())->create([
            'accident_status_id' => (new AccidentStatusService())->getNewStatus()->getAttribute('id'),
            'caseable_type' => DoctorAccident::class,
        ])->getAttribute('id');

        $response2 = $this->sendGet('/api/director/cases/' . $accidentId . '/scenario');
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => 'doctor',
                    'order' => '1',
                    'mode' => 'step',
                    'accidentStatusId' => '1',
                    'status' => 'current',
                    'title' => 'new',
                ],
                [
                    'id' => 2,
                    'tag' => 'doctor',
                    'order' => '2',
                    'mode' => 'step',
                    'accidentStatusId' => '2',
                    'status' => '',
                    'title' => 'assigned',
                ],
                [
                    'id' => 3,
                    'tag' => 'doctor',
                    'order' => '3',
                    'mode' => 'step',
                    'accidentStatusId' => '3',
                    'status' => '',
                    'title' => 'in_progress',
                ],
                [
                    'id' => 4,
                    'tag' => 'doctor',
                    'order' => '4',
                    'mode' => 'step',
                    'accidentStatusId' => '4',
                    'status' => '',
                    'title' => 'sent',
                ],
                [
                    'id' => 5,
                    'tag' => 'doctor',
                    'order' => '5',
                    'mode' => 'step',
                    'accidentStatusId' => '5',
                    'status' => '',
                    'title' => 'paid',
                ],
                [
                    'id' => 7,
                    'tag' => 'doctor',
                    'order' => '7',
                    'mode' => 'step',
                    'accidentStatusId' => '7',
                    'status' => '',
                    'title' => 'closed',
                ],
            ],
        ]);
    }

    public function testDoctorCaseScenarioStoryAllSteps(): void
    {
        // status new accident
        $accident = factory(Accident::class)->create([
            'caseable_type' => DoctorAccident::class,
            'caseable_id' => factory(DoctorAccident::class)->create([
                'doctor_id' => 0,
            ])->id,
        ]);

        $accident->caseable->fill([
            'doctor_id' => factory(Doctor::class)->create()->id,
        ])->save();

        $accidentService = new AccidentService();

        // Doctor needs to visit accidents page to set status `in_progress`
        $accident->refresh();
        $accidentService->moveDoctorAccidentToInProgressState($accident);

        // closing an accident
        $accidentService->closeAccident($accident);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => 'doctor',
                    'order' => '1',
                    'mode' => 'step',
                    'accidentStatusId' => '1',
                    'status' => 'visited',
                    'title' => 'new',
                ],
                [
                    'id' => 2,
                    'tag' => 'doctor',
                    'order' => '2',
                    'mode' => 'step',
                    'accidentStatusId' => '2',
                    'status' => 'visited',
                    'title' => 'assigned',
                ],
                [
                    'id' => 3,
                    'tag' => 'doctor',
                    'order' => '3',
                    'mode' => 'step',
                    'accidentStatusId' => '3',
                    'status' => 'visited',
                    'title' => 'in_progress',
                ],
                [
                    'id' => 4,
                    'tag' => 'doctor',
                    'order' => '4',
                    'mode' => 'step',
                    'accidentStatusId' => '4',
                    'status' => '',
                    'title' => 'sent',
                ],
                [
                    'id' => 5,
                    'tag' => 'doctor',
                    'order' => '5',
                    'mode' => 'step',
                    'accidentStatusId' => '5',
                    'status' => '',
                    'title' => 'paid',
                ],
                [
                    'id' => 7,
                    'tag' => 'doctor',
                    'order' => '7',
                    'mode' => 'step',
                    'accidentStatusId' => '7',
                    'status' => 'current',
                    'title' => 'closed',
                ],
            ],
        ]);
    }

    public function testDoctorCaseScenarioStorySkippedStep(): void
    {
        // status new accident
        $accident = factory(Accident::class)->create([
            'caseable_type' => DoctorAccident::class,
            'caseable_id' => factory(DoctorAccident::class)->create([
                'doctor_id' => 0,
            ])->id,
        ]);

        $accidentService = new AccidentService();
        $accidentService->rejectDoctorAccident($accident);

        $rejectStatus = AccidentStatus::firstOrCreate([
            'title' => AccidentStatusService::STATUS_REJECT,
            'type' => AccidentStatusService::TYPE_DOCTOR,
        ]);
        self::assertEquals($accident->accident_status_id, $rejectStatus->id);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => 'doctor',
                    'order' => '1',
                    'mode' => 'step',
                    'accidentStatusId' => '1',
                    'status' => 'visited',
                    'title' => 'new',
                ],
                [
                    'id' => 6,
                    'tag' => 'doctor',
                    'order' => '6',
                    'mode' => 'skip:doctor',
                    'accidentStatusId' => '6',
                    'status' => 'current',
                    'title' => 'reject',
                ],
                [
                    'id' => 7,
                    'tag' => 'doctor',
                    'order' => '7',
                    'mode' => 'step',
                    'accidentStatusId' => '7',
                    'status' => '',
                    'title' => 'closed',
                ],
            ],
        ]);
    }

    public function testDoctorCaseScenarioStorySkippedClosedStep(): void
    {
        // status new accident
        $accident = factory(Accident::class)->create([
            'caseable_type' => DoctorAccident::class,
            'caseable_id' => factory(DoctorAccident::class)->create([
                'doctor_id' => 0,
            ])->id,
        ]);

        $accidentService = new AccidentService();
        $accidentService->rejectDoctorAccident($accident);

        $rejectStatus = AccidentStatus::firstOrCreate([
            'title' => AccidentStatusService::STATUS_REJECT,
            'type' => AccidentStatusService::TYPE_DOCTOR,
        ]);
        self::assertEquals($accident->accident_status_id, $rejectStatus->id);

        // closing an accident
        $accidentService->closeAccident($accident);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => 'doctor',
                    'order' => '1',
                    'mode' => 'step',
                    'accidentStatusId' => '1',
                    'status' => 'visited',
                    'title' => 'new',
                ],
                [
                    'id' => 6,
                    'tag' => 'doctor',
                    'order' => '6',
                    'mode' => 'skip:doctor',
                    'accidentStatusId' => '6',
                    'status' => 'visited',
                    'title' => 'reject',
                ],
                [
                    'id' => 7,
                    'tag' => 'doctor',
                    'order' => '7',
                    'mode' => 'step',
                    'accidentStatusId' => '7',
                    'status' => 'current',
                    'title' => 'closed',
                ],
            ],
        ]);
    }
}
