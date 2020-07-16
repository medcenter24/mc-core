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
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\App\Services\Entity\DoctorAccidentService;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\App\Services\Entity\HospitalAccidentService;
use medcenter24\mcCore\App\Services\Entity\InvoiceService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;
use ScenariosTableSeeder;

class CaseControllerScenarioActionTest extends TestCase
{
    use DirectorTestTraitApi;

    private CaseAccidentService $caseAccidentService;
    private InvoiceService $invoiceService;
    private AccidentService $accidentService;
    private AccidentStatusService $accidentStatusService;
    private HospitalAccidentService $hospitalAccidentService;
    private DoctorAccidentService $doctorAccidentService;
    private DoctorService $doctorService;

    public function setUp(): void
    {
        parent::setUp();
        // adding scenarios to the storage
        (new ScenariosTableSeeder())->run();
        $this->caseAccidentService = new CaseAccidentService();
        $this->invoiceService = new InvoiceService();
        $this->accidentService = new AccidentService();
        $this->accidentStatusService = new AccidentStatusService();
        $this->hospitalAccidentService = new HospitalAccidentService();
        $this->doctorAccidentService = new DoctorAccidentService();
        $this->doctorService = new DoctorService();
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
                        'accidentStatusType' => 'accident',
                    ],
                    [
                        'id' => 2,
                        'tag' => 'doctor',
                        'order' => 2,
                        'mode' => 'step',
                        'accidentStatusId' => 2,
                        'status' => '',
                        'title' => AccidentStatusService::STATUS_ASSIGNED,
                        'accidentStatusType' => 'doctor',
                    ],
                    [
                        'id' => 3,
                        'tag' => 'doctor',
                        'order' => 3,
                        'mode' => 'step',
                        'accidentStatusId' => 3,
                        'status' => '',
                        'title' => AccidentStatusService::STATUS_IN_PROGRESS,
                        'accidentStatusType' => 'doctor',
                    ],
                    [
                        'id' => 4,
                        'tag' => 'doctor',
                        'order' => 4,
                        'mode' => 'step',
                        'accidentStatusId' => 4,
                        'status' => '',
                        'title' => AccidentStatusService::STATUS_SENT,
                        'accidentStatusType' => 'doctor',
                    ],
                    [
                        'id' => 5,
                        'tag' => 'doctor',
                        'order' => 5,
                        'mode' => 'step',
                        'accidentStatusId' => 5,
                        'status' => '',
                        'title' => AccidentStatusService::STATUS_PAID,
                        'accidentStatusType' => 'doctor',
                    ],
                    [
                        'id' => 7,
                        'tag' => 'doctor',
                        'order' => 7,
                        'mode' => 'step',
                        'accidentStatusId' => 7,
                        'status' => '',
                        'title' => AccidentStatusService::STATUS_PAID,
                        'accidentStatusType' => 'assistant',
                    ],
                    [
                        'id' => 8,
                        'tag' => 'doctor',
                        'order' => 8,
                        'mode' => 'step',
                        'accidentStatusId' => 8,
                        'status' => '',
                        'title' => 'closed',
                        'accidentStatusType' => 'accident',
                    ]
                ],
            ]);
    }

    /**
     * @throws InconsistentDataException
     */
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
                        'id' => 9,
                        'tag' => 'hospital',
                        'order' => 1,
                        'mode' => 'step',
                        'accidentStatusId' => 1,
                        'status' => 'current',
                        'title' => 'new',
                        'accidentStatusType' => 'accident',
                    ],
                    [
                        'id' => 10,
                        'tag' => 'hospital',
                        'order' => 2,
                        'mode' => 'step',
                        'accidentStatusId' => 9,
                        'status' => '',
                        'title' => 'assistant_guarantee',
                        'accidentStatusType' => 'assistant',
                    ],
                    [
                        'id' => 11,
                        'tag' => 'hospital',
                        'order' => 3,
                        'mode' => 'step',
                        'accidentStatusId' => 10,
                        'status' => '',
                        'title' => 'assigned',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 12,
                        'tag' => 'hospital',
                        'order' => 4,
                        'mode' => 'step',
                        'accidentStatusId' => 11,
                        'status' => '',
                        'title' => 'hospital_guarantee',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 13,
                        'tag' => 'hospital',
                        'order' => 5,
                        'mode' => 'step',
                        'accidentStatusId' => 12,
                        'status' => '',
                        'title' => 'hospital_invoice',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 14,
                        'tag' => 'hospital',
                        'order' => 6,
                        'mode' => 'step',
                        'accidentStatusId' => 13,
                        'status' => '',
                        'title' => 'paid',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 15,
                        'tag' => 'hospital',
                        'order' => 7,
                        'mode' => 'step',
                        'accidentStatusId' => 14,
                        'status' => '',
                        'title' => 'assistant_invoice',
                        'accidentStatusType' => 'assistant',
                    ],
                    [
                        'id' => 16,
                        'tag' => 'hospital',
                        'order' => 8,
                        'mode' => 'step',
                        'accidentStatusId' => 7,
                        'status' => '',
                        'title' => 'paid',
                        'accidentStatusType' => 'assistant',
                    ],
                    [
                        'id' => 17,
                        'tag' => 'hospital',
                        'order' => 9,
                        'mode' => 'step',
                        'accidentStatusId' => 8,
                        'status' => '',
                        'title' => 'closed',
                        'accidentStatusType' => 'accident',
                    ]
                ],
        ]);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testHospitalCaseScenarioPassAllSteps(): void
    {
        $invoice = $this->invoiceService->create();

        $accident = $this->caseAccidentService->create([
            CaseAccidentService::PROPERTY_ACCIDENT => [
                // invoice can't be paid on creation (it won't work with events when accident does not exists)
                AccidentService::FIELD_ASSISTANT_INVOICE_ID => $invoice->id,
                AccidentService::FIELD_ASSISTANT_GUARANTEE_ID => factory(Invoice::class)->create()->id,
                AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
            ],
            CaseAccidentService::PROPERTY_HOSPITAL_ACCIDENT => [
                HospitalAccidentService::FIELD_HOSPITAL_ID => factory(Hospital::class)->create()->id,
                HospitalAccidentService::FIELD_HOSPITAL_GUARANTEE_ID => factory(Invoice::class)->create()->id,
                HospitalAccidentService::FIELD_HOSPITAL_INVOICE_ID => factory(Invoice::class)->create()->id,
            ]
        ]);

        // adding paid step into the history
        $this->invoiceService->findAndUpdate([InvoiceService::FIELD_ID], [
            InvoiceService::FIELD_ID => $invoice->id,
            InvoiceService::FIELD_STATUS => InvoiceService::STATUS_PAID,
        ]);
        (new AccidentService())->closeAccident($accident);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 9,
                        'tag' => 'hospital',
                        'order' => 1,
                        'mode' => 'step',
                        'accidentStatusId' => 1,
                        'status' => 'visited',
                        'title' => 'new',
                        'accidentStatusType' => 'accident',
                    ],
                    [
                        'id' => 10,
                        'tag' => 'hospital',
                        'order' => 2,
                        'mode' => 'step',
                        'accidentStatusId' => 9,
                        'status' => 'visited',
                        'title' => 'assistant_guarantee',
                        'accidentStatusType' => 'assistant',
                    ],
                    [
                        'id' => 11,
                        'tag' => 'hospital',
                        'order' => 3,
                        'mode' => 'step',
                        'accidentStatusId' => 10,
                        'status' => 'visited',
                        'title' => 'assigned',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 12,
                        'tag' => 'hospital',
                        'order' => 4,
                        'mode' => 'step',
                        'accidentStatusId' => 11,
                        'status' => 'visited',
                        'title' => 'hospital_guarantee',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 13,
                        'tag' => 'hospital',
                        'order' => 5,
                        'mode' => 'step',
                        'accidentStatusId' => 12,
                        'status' => 'visited',
                        'title' => 'hospital_invoice',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 14,
                        'tag' => 'hospital',
                        'order' => 6,
                        'mode' => 'step',
                        'accidentStatusId' => 13,
                        'status' => '',
                        'title' => 'paid',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 15,
                        'tag' => 'hospital',
                        'order' => 7,
                        'mode' => 'step',
                        'accidentStatusId' => 14,
                        'status' => 'visited',
                        'title' => 'assistant_invoice',
                        'accidentStatusType' => 'assistant',
                    ],

                ],
        ]);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testHospitalCaseScenarioCreateAndClose(): void
    {
        /** @var Accident $accident */
        $accident = $this->accidentService->create([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => 0,
            'assistant_invoice_id' => 0,
            'assistant_payment_id' => 0,
            'assistant_guarantee_id' => 0,
        ]);

        $this->accidentService->closeAccident($accident);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertStatus(200);
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 9,
                        'tag' => 'hospital',
                        'order' => 1,
                        'mode' => 'step',
                        'accidentStatusId' => 1,
                        'status' => 'visited',
                        'title' => 'new',
                        'accidentStatusType' => 'accident',
                    ],
                    [
                        'id' => 10,
                        'tag' => 'hospital',
                        'order' => 2,
                        'mode' => 'step',
                        'accidentStatusId' => 9,
                        'status' => '',
                        'title' => 'assistant_guarantee',
                        'accidentStatusType' => 'assistant',
                    ],
                    [
                        'id' => 11,
                        'tag' => 'hospital',
                        'order' => 3,
                        'mode' => 'step',
                        'accidentStatusId' => 10,
                        'status' => '',
                        'title' => 'assigned',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 12,
                        'tag' => 'hospital',
                        'order' => 4,
                        'mode' => 'step',
                        'accidentStatusId' => 11,
                        'status' => '',
                        'title' => 'hospital_guarantee',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 13,
                        'tag' => 'hospital',
                        'order' => 5,
                        'mode' => 'step',
                        'accidentStatusId' => 12,
                        'status' => '',
                        'title' => 'hospital_invoice',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 14,
                        'tag' => 'hospital',
                        'order' => 6,
                        'mode' => 'step',
                        'accidentStatusId' => 13,
                        'status' => '',
                        'title' => 'paid',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 15,
                        'tag' => 'hospital',
                        'order' => 7,
                        'mode' => 'step',
                        'accidentStatusId' => 14,
                        'status' => '',
                        'title' => 'assistant_invoice',
                        'accidentStatusType' => 'assistant',
                    ],
                    [
                        'id' => 16,
                        'tag' => 'hospital',
                        'order' => 8,
                        'mode' => 'step',
                        'accidentStatusId' => 7,
                        'status' => '',
                        'title' => 'paid',
                        'accidentStatusType' => 'assistant',
                    ],
                    [
                        'id' => 17,
                        'tag' => 'hospital',
                        'order' => 9,
                        'mode' => 'step',
                        'accidentStatusId' => 8,
                        'status' => 'current',
                        'title' => 'closed',
                        'accidentStatusType' => 'accident',
                    ]
                ],
        ]);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testHospitalCaseScenarioPartialSteps(): void
    {
        /** @var Accident $accident */
        $accident = $this->accidentService->create([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => $this->hospitalAccidentService->create([
                'hospital_id' => 0,
                'hospital_guarantee_id' => 0,
                'hospital_invoice_id' => 0,
            ])->id,
            'assistant_invoice_id' => 0,
            'assistant_payment_id' => factory(Payment::class)->create()->id,
            'assistant_guarantee_id' => 0,
            'accident_status_id' => (new AccidentStatusService())->getNewStatus(),
        ]);

        $this->hospitalAccidentService->findAndUpdate([HospitalAccidentService::FIELD_ID], [
            HospitalAccidentService::FIELD_ID => $accident->caseable_id,
            'hospital_id' => factory(Hospital::class)->create()->id,
            'hospital_guarantee_id' => 0,
            'hospital_invoice_id' => 0,
        ]);

        $this->accidentService->closeAccident($accident);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 9,
                        'tag' => 'hospital',
                        'order' => 1,
                        'mode' => 'step',
                        'accidentStatusId' => 1,
                        'status' => 'visited',
                        'title' => 'new',
                        'accidentStatusType' => 'accident',
                    ],
                    [
                        'id' => 10,
                        'tag' => 'hospital',
                        'order' => 2,
                        'mode' => 'step',
                        'accidentStatusId' => 9,
                        'status' => '',
                        'title' => 'assistant_guarantee',
                        'accidentStatusType' => 'assistant',
                    ],
                    [
                        'id' => 11,
                        'tag' => 'hospital',
                        'order' => 3,
                        'mode' => 'step',
                        'accidentStatusId' => 10,
                        'status' => '',
                        'title' => 'assigned',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 12,
                        'tag' => 'hospital',
                        'order' => 4,
                        'mode' => 'step',
                        'accidentStatusId' => 11,
                        'status' => '',
                        'title' => 'hospital_guarantee',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 13,
                        'tag' => 'hospital',
                        'order' => 5,
                        'mode' => 'step',
                        'accidentStatusId' => 12,
                        'status' => '',
                        'title' => 'hospital_invoice',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 14,
                        'tag' => 'hospital',
                        'order' => 6,
                        'mode' => 'step',
                        'accidentStatusId' => 13,
                        'status' => '',
                        'title' => 'paid',
                        'accidentStatusType' => 'hospital',
                    ],
                    [
                        'id' => 15,
                        'tag' => 'hospital',
                        'order' => 7,
                        'mode' => 'step',
                        'accidentStatusId' => 14,
                        'status' => '',
                        'title' => 'assistant_invoice',
                        'accidentStatusType' => 'assistant',
                    ],
                    [
                        'id' => 16,
                        'tag' => 'hospital',
                        'order' => 8,
                        'mode' => 'step',
                        'accidentStatusId' => 7,
                        'status' => '',
                        'title' => 'paid',
                        'accidentStatusType' => 'assistant',
                    ],
                    [
                        'id' => 17,
                        'tag' => 'hospital',
                        'order' => 9,
                        'mode' => 'step',
                        'accidentStatusId' => 8,
                        'status' => 'current',
                        'title' => 'closed',
                        'accidentStatusType' => 'accident',
                    ]
                ],
        ]);
    }

    public function testDoctorCaseScenarioNew(): void
    {
        $accidentId = $this->accidentService->create([
            'accident_status_id' => $this->accidentStatusService->getNewStatus()->getAttribute('id'),
            'caseable_type' => DoctorAccident::class,
        ])->getAttribute('id');

        $response2 = $this->sendGet('/api/director/cases/' . $accidentId . '/scenario');
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => 'doctor',
                    'order' => 1,
                    'mode' => 'step',
                    'accidentStatusId' => 1,
                    'status' => 'current',
                    'title' => 'new',
                    'accidentStatusType' => 'accident',
                ],
                [
                    'id' => 2,
                    'tag' => 'doctor',
                    'order' => 2,
                    'mode' => 'step',
                    'accidentStatusId' => 2,
                    'status' => '',
                    'title' => 'assigned',
                    'accidentStatusType' => 'doctor',
                ],
                [
                    'id' => 3,
                    'tag' => 'doctor',
                    'order' => 3,
                    'mode' => 'step',
                    'accidentStatusId' => 3,
                    'status' => '',
                    'title' => 'in_progress',
                    'accidentStatusType' => 'doctor',
                ],
                [
                    'id' => 4,
                    'tag' => 'doctor',
                    'order' => 4,
                    'mode' => 'step',
                    'accidentStatusId' => 4,
                    'status' => '',
                    'title' => 'sent',
                    'accidentStatusType' => 'doctor',
                ],
                [
                    'id' => 5,
                    'tag' => 'doctor',
                    'order' => 5,
                    'mode' => 'step',
                    'accidentStatusId' => 5,
                    'status' => '',
                    'title' => 'paid',
                    'accidentStatusType' => 'doctor',
                ],
                [
                    'id' => 7,
                    'tag' => 'doctor',
                    'order' => 7,
                    'mode' => 'step',
                    'accidentStatusId' => 7,
                    'status' => '',
                    'title' => 'paid',
                    'accidentStatusType' => 'assistant',
                ],
                [
                    'id' => 8,
                    'tag' => 'doctor',
                    'order' => 8,
                    'mode' => 'step',
                    'accidentStatusId' => 8,
                    'status' => '',
                    'title' => 'closed',
                    'accidentStatusType' => 'accident',
                ]
            ],
        ]);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testDoctorCaseScenarioStoryAllSteps(): void
    {
        // just good to know
        // it wont change accidents statuses

        // status new accident
        /** @var Accident $accident */
        /*$accident = $this->accidentService->create([
            'caseable_type' => DoctorAccident::class,
            'caseable_id' => factory(DoctorAccident::class)->create([
                'doctor_id' => 0,
            ])->id,
        ]);*/

        /*$this->doctorAccidentService->findAndUpdate([DoctorAccidentService::FIELD_ID], [
            'id' => $accident->caseable_id,
            'doctor_id' => factory(Doctor::class)->create()->id, // todo assigned doesn't work to be fixed
        ])->save();*/

        // when it does change accidents statuses
        $accident = $this->caseAccidentService->create([
            CaseAccidentService::PROPERTY_ACCIDENT => [
                AccidentService::FIELD_CASEABLE_TYPE => DoctorAccident::class,
            ],
            CaseAccidentService::PROPERTY_DOCTOR_ACCIDENT => [
                DoctorAccidentService::FIELD_DOCTOR_ID => $this->doctorService->create()->id,
            ]
        ]);

        // Doctor needs to visit accidents page to set status `in_progress`
        $this->accidentService->moveDoctorAccidentToInProgressState($accident);

        // closing an accident
        $this->accidentService->closeAccident($accident);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => 'doctor',
                    'order' => 1,
                    'mode' => 'step',
                    'accidentStatusId' => 1,
                    'status' => 'visited',
                    'title' => 'new',
                    'accidentStatusType' => 'accident',
                ],
                [
                    'id' => 2,
                    'tag' => 'doctor',
                    'order' => 2,
                    'mode' => 'step',
                    'accidentStatusId' => 2,
                    'status' => 'visited',
                    'title' => 'assigned',
                    'accidentStatusType' => 'doctor',
                ],
                [
                    'id' => 3,
                    'tag' => 'doctor',
                    'order' => 3,
                    'mode' => 'step',
                    'accidentStatusId' => 3,
                    'status' => 'visited',
                    'title' => 'in_progress',
                ],
                [
                    'id' => 4,
                    'tag' => 'doctor',
                    'order' => 4,
                    'mode' => 'step',
                    'accidentStatusId' => 4,
                    'status' => '',
                    'title' => 'sent',
                    'accidentStatusType' => 'doctor',
                ],
                [
                    'id' => 5,
                    'tag' => 'doctor',
                    'order' => 5,
                    'mode' => 'step',
                    'accidentStatusId' => 5,
                    'status' => '',
                    'title' => 'paid',
                    'accidentStatusType' => 'doctor',
                ],
                [
                    'id' => 7,
                    'tag' => 'doctor',
                    'order' => 7,
                    'mode' => 'step',
                    'accidentStatusId' => 7,
                    'status' => '',
                    'title' => 'paid',
                    'accidentStatusType' => 'assistant',
                ],
                [
                    'id' => 8,
                    'tag' => 'doctor',
                    'order' => 8,
                    'mode' => 'step',
                    'accidentStatusId' => 8,
                    'status' => 'current',
                    'title' => 'closed',
                    'accidentStatusType' => 'accident',
                ]
            ],
        ]);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testDoctorCaseScenarioStorySkippedStep(): void
    {
        $accident = $this->caseAccidentService->create([
            CaseAccidentService::PROPERTY_ACCIDENT => [
                AccidentService::FIELD_CASEABLE_TYPE => DoctorAccident::class,
            ],
        ]);

        $this->accidentService->rejectDoctorAccident($accident);

        $rejectStatus = $this->accidentStatusService->getDoctorRejectedStatus();
        self::assertEquals($accident->accident_status_id, $rejectStatus->id);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => 'doctor',
                    'order' => 1,
                    'mode' => 'step',
                    'accidentStatusId' => 1,
                    'status' => 'visited',
                    'title' => 'new',
                    'accidentStatusType' => 'accident',
                ],
                [
                    'id' => 6,
                    'tag' => 'doctor',
                    'order' => 6,
                    'mode' => 'skip:doctor',
                    'accidentStatusId' => 6,
                    'status' => 'current',
                    'title' => 'reject',
                    'accidentStatusType' => 'doctor',
                ],
                [
                    'id' => 7,
                    'tag' => 'doctor',
                    'order' => 7,
                    'mode' => 'step',
                    'accidentStatusId' => 7,
                    'status' => '',
                    'title' => 'paid',
                    'accidentStatusType' => 'assistant',
                ],
                [
                    'id' => 8,
                    'tag' => 'doctor',
                    'order' => 8,
                    'mode' => 'step',
                    'accidentStatusId' => 8,
                    'status' => '',
                    'title' => 'closed',
                    'accidentStatusType' => 'accident',
                ]
            ],
        ]);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testDoctorCaseScenarioStorySkippedClosedStep(): void
    {
        $accident = $this->caseAccidentService->create([
            CaseAccidentService::PROPERTY_ACCIDENT => [
                AccidentService::FIELD_CASEABLE_TYPE => DoctorAccident::class,
            ],
        ]);

        $this->accidentService->rejectDoctorAccident($accident);

        $rejectStatus = $this->accidentStatusService->getDoctorRejectedStatus();
        self::assertEquals($accident->accident_status_id, $rejectStatus->id);

        // closing an accident
        $this->accidentService->closeAccident($accident);

        $response2 = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => 'doctor',
                    'order' => 1,
                    'mode' => 'step',
                    'accidentStatusId' => 1,
                    'status' => 'visited',
                    'title' => 'new',
                    'accidentStatusType' => 'accident',
                ],
                [
                    'id' => 6,
                    'tag' => 'doctor',
                    'order' => 6,
                    'mode' => 'skip:doctor',
                    'accidentStatusId' => 6,
                    'status' => 'visited',
                    'title' => 'reject',
                    'accidentStatusType' => 'doctor',
                ],
                [
                    'id' => 7,
                    'tag' => 'doctor',
                    'order' => 7,
                    'mode' => 'step',
                    'accidentStatusId' => 7,
                    'status' => '',
                    'title' => 'paid',
                    'accidentStatusType' => 'assistant',
                ],
                [
                    'id' => 8,
                    'tag' => 'doctor',
                    'order' => 8,
                    'mode' => 'step',
                    'accidentStatusId' => 8,
                    'status' => 'current',
                    'title' => 'closed',
                    'accidentStatusType' => 'accident',
                ]
            ],
        ]);
    }
}
