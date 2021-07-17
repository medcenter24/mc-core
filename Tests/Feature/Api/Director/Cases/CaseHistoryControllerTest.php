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

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases;

use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Hospital;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Entity\Invoice;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\App\Services\Entity\DoctorAccidentService;
use medcenter24\mcCore\App\Services\Entity\HospitalAccidentService;
use medcenter24\mcCore\App\Services\Entity\InvoiceService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class CaseHistoryControllerTest extends TestCase
{
    use DirectorTestTraitApi;

    private CaseAccidentService $caseAccidentService;
    private InvoiceService $invoiceService;
    private AccidentStatusService $accidentStatusService;
    private AccidentService $accidentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->caseAccidentService = new CaseAccidentService();
        $this->invoiceService = new InvoiceService();
        $this->accidentStatusService = new AccidentStatusService();
        $this->accidentService = new AccidentService();
    }

    /**
     * @throws InconsistentDataException
     */
    public function testHistory(): void
    {
        $accident = $this->caseAccidentService->create();
        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/history');
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'id' => 1,
                        'userId' => 0,
                        'accidentStatusId' => 1,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'new',
                    ],
                ],
            ]
        );
    }

    /**
     * All possible statuses of the Hospital Case
     * @throws InconsistentDataException
     */
    public function testHistoryHospitalCase(): void
    {
        $invoice = $this->invoiceService->create();

        $accident = $this->caseAccidentService->create([
            CaseAccidentService::PROPERTY_ACCIDENT => [
                // invoice can't be paid on creation (it won't work with events when accident does not exists)
                AccidentService::FIELD_ASSISTANT_INVOICE_ID => $invoice->id,
                AccidentService::FIELD_ASSISTANT_GUARANTEE_ID => Invoice::factory()->create()->id,
                AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
            ],
            CaseAccidentService::PROPERTY_HOSPITAL_ACCIDENT => [
                HospitalAccidentService::FIELD_HOSPITAL_ID => Hospital::factory()->create()->id,
                HospitalAccidentService::FIELD_HOSPITAL_GUARANTEE_ID => Invoice::factory()->create()->id,
                HospitalAccidentService::FIELD_HOSPITAL_INVOICE_ID => Invoice::factory()->create()->id,
            ]
        ]);

        // adding paid step into the history
        $this->invoiceService->findAndUpdate([InvoiceService::FIELD_ID], [
            InvoiceService::FIELD_ID => $invoice->id,
            InvoiceService::FIELD_STATUS => InvoiceService::STATUS_PAID,
        ]);

        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/history');
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'id' => 1,
                        'userId' => 0,
                        'accidentStatusId' => 1,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'new',
                    ],
                    [
                        'id' => 2,
                        'userId' => 0,
                        'accidentStatusId' => 3,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'assistant_invoice',
                    ],
                    [
                        'id' => 3,
                        'userId' => 0,
                        'accidentStatusId' => 4,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'assistant_guarantee',
                    ],
                    [
                        'id' => 4,
                        'userId' => 0,
                        'accidentStatusId' => 5,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'assigned',
                    ],
                    [
                        'id' => 5,
                        'userId' => 0,
                        'accidentStatusId' => 6,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'hospital_guarantee',
                    ],
                    [
                        'id' => 6,
                        'userId' => 0,
                        'accidentStatusId' => 7,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'hospital_invoice',
                    ],
                    [
                        'id' => 7,
                        'userId' => 0,
                        'accidentStatusId' => 8,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'paid',
                    ]
                ],
            ]
        );
    }

    /**
     * Doctor Case
     * @throws InconsistentDataException
     */
    public function testHistoryDoctorCase(): void
    {
        $invoice = $this->invoiceService->create();

        $accident = $this->caseAccidentService->create([
            CaseAccidentService::PROPERTY_ACCIDENT => [
                // invoice can't be paid on creation (it won't work with events when accident does not exists)
                AccidentService::FIELD_ASSISTANT_INVOICE_ID => $invoice->id,
                AccidentService::FIELD_ASSISTANT_GUARANTEE_ID => Invoice::factory()->create()->id,
                AccidentService::FIELD_CASEABLE_TYPE => DoctorAccident::class,
            ],
            CaseAccidentService::PROPERTY_DOCTOR_ACCIDENT => [
                DoctorAccidentService::FIELD_DOCTOR_ID => Doctor::factory()->create()->id,
            ]
        ]);

        // adding paid step into the history
        $this->invoiceService->findAndUpdate([InvoiceService::FIELD_ID], [
            InvoiceService::FIELD_ID => $invoice->id,
            InvoiceService::FIELD_STATUS => InvoiceService::STATUS_PAID,
        ]);

        $this->accidentService->moveDoctorAccidentToInProgressState($accident);
        $this->accidentService->setStatus($accident, $this->accidentStatusService->getDoctorSentStatus());
        $this->accidentService->rejectDoctorAccident($accident);
        $this->accidentService->closeAccident($accident);

        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/history');
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'id' => 1,
                        'userId' => 0,
                        'accidentStatusId' => 1,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'new',
                    ],
                    [
                        'id' => 2,
                        'userId' => 0,
                        'accidentStatusId' => 3,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'assistant_invoice',
                    ],
                    [
                        'id' => 3,
                        'userId' => 0,
                        'accidentStatusId' => 4,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'assistant_guarantee',
                    ],
                    [
                        'id' => 4,
                        'userId' => 0,
                        'accidentStatusId' => 5,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'assigned',
                    ],
                    [
                        'id' => 5,
                        'userId' => 0,
                        'accidentStatusId' => 6,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'paid',
                    ],
                    [
                        'id' => 6,
                        'userId' => 0,
                        'accidentStatusId' => 7,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => 'moved',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'in_progress',
                    ],
                    [
                        'id' => 7,
                        'userId' => 0,
                        'accidentStatusId' => 8,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => '',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'sent',
                    ],
                    [
                        'id' => 8,
                        'userId' => 0,
                        'accidentStatusId' => 9,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => 'rejected',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'reject',
                    ],
                    [
                        'id' => 9,
                        'userId' => 0,
                        'accidentStatusId' => 2,
                        'historyableId' => 1,
                        'historyableType' => 'accident',
                        'commentary' => 'closed',
                        'userName' => '',
                        'userThumb' => '',
                        'statusTitle' => 'closed',
                    ]
                ],
            ]
        );
    }
}
