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

use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\AccidentStatus;
use medcenter24\mcCore\App\Doctor;
use medcenter24\mcCore\App\DoctorAccident;
use medcenter24\mcCore\App\Hospital;
use medcenter24\mcCore\App\HospitalAccident;
use medcenter24\mcCore\App\Invoice;
use medcenter24\mcCore\App\Payment;
use medcenter24\mcCore\App\Services\AccidentService;
use medcenter24\mcCore\App\Services\AccidentStatusesService;
use medcenter24\mcCore\App\Upload;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\Tests\Feature\Api\JwtHeaders;
use medcenter24\mcCore\Tests\Feature\Api\LoggedUser;
use medcenter24\mcCore\Tests\TestCase;
use ScenariosTableSeeder;

class CaseControllerScenarioActionTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function setUp(): void
    {
        parent::setUp();
        // adding scenarios to the storage
        (new ScenariosTableSeeder())->run();
    }

    public function testGetDefaultScenario(): void
    {
        $response = $this->post('/api/director/cases', $caseData = [], $this->headers($this->getUser()));
        $response->assertStatus(201);
        $accident = $response->json()['accident'];

        $response2 = $this->get('/api/director/cases/' . $accident['id'] . '/scenario', $this->headers($this->getUser()));
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 8,
                        'tag' => HospitalAccident::class,
                        'order' => '1',
                        'mode' => 'step',
                        'accident_status_id' => '1',
                        'status' => 'current',
                        'title' => 'new',
                    ],
                    [
                        'id' => 9,
                        'tag' => HospitalAccident::class,
                        'order' => '2',
                        'mode' => 'step',
                        'accident_status_id' => '8',
                        'status' => '',
                        'title' => 'assistant_guarantee',
                    ],
                    [
                        'id' => 10,
                        'tag' => HospitalAccident::class,
                        'order' => '3',
                        'mode' => 'step',
                        'accident_status_id' => '9',
                        'status' => '',
                        'title' => 'assigned',
                    ],
                    [
                        'id' => 11,
                        'tag' => HospitalAccident::class,
                        'order' => '4',
                        'mode' => 'step',
                        'accident_status_id' => '10',
                        'status' => '',
                        'title' => 'hospital_guarantee',
                    ],
                    [
                        'id' => 12,
                        'tag' => HospitalAccident::class,
                        'order' => '5',
                        'mode' => 'step',
                        'accident_status_id' => '11',
                        'status' => '',
                        'title' => 'hospital_invoice',
                    ],
                    [
                        'id' => 13,
                        'tag' => HospitalAccident::class,
                        'order' => '6',
                        'mode' => 'step',
                        'accident_status_id' => '12',
                        'status' => '',
                        'title' => 'assistant_invoice',
                    ],
                    [
                        'id' => 14,
                        'tag' => HospitalAccident::class,
                        'order' => '7',
                        'mode' => 'step',
                        'accident_status_id' => '13',
                        'status' => '',
                        'title' => 'paid',
                    ],
                    [
                        'id' => 15,
                        'tag' => HospitalAccident::class,
                        'order' => '8',
                        'mode' => 'step',
                        'accident_status_id' => '7',
                        'status' => '',
                        'title' => 'closed',
                    ],
                ],
            ]);
    }

    public function testHospitalCaseScenarioCurrentNew(): void
    {
        $accidentId = factory(Accident::class)->create([
            'caseable_type' => HospitalAccident::class,
        ])->id;

        $response2 = $this->get('/api/director/cases/' . $accidentId . '/scenario',
            $this->headers($this->getUser()));
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 8,
                        'tag' => HospitalAccident::class,
                        'order' => '1',
                        'mode' => 'step',
                        'accident_status_id' => '1',
                        'status' => 'current',
                        'title' => 'new',
                    ],
                    [
                        'id' => 9,
                        'tag' => HospitalAccident::class,
                        'order' => '2',
                        'mode' => 'step',
                        'accident_status_id' => '8',
                        'status' => '',
                        'title' => 'assistant_guarantee',
                    ],
                    [
                        'id' => 10,
                        'tag' => HospitalAccident::class,
                        'order' => '3',
                        'mode' => 'step',
                        'accident_status_id' => '9',
                        'status' => '',
                        'title' => 'assigned',
                    ],
                    [
                        'id' => 11,
                        'tag' => HospitalAccident::class,
                        'order' => '4',
                        'mode' => 'step',
                        'accident_status_id' => '10',
                        'status' => '',
                        'title' => 'hospital_guarantee',
                    ],
                    [
                        'id' => 12,
                        'tag' => HospitalAccident::class,
                        'order' => '5',
                        'mode' => 'step',
                        'accident_status_id' => '11',
                        'status' => '',
                        'title' => 'hospital_invoice',
                    ],
                    [
                        'id' => 13,
                        'tag' => HospitalAccident::class,
                        'order' => '6',
                        'mode' => 'step',
                        'accident_status_id' => '12',
                        'status' => '',
                        'title' => 'assistant_invoice',
                    ],
                    [
                        'id' => 14,
                        'tag' => HospitalAccident::class,
                        'order' => '7',
                        'mode' => 'step',
                        'accident_status_id' => '13',
                        'status' => '',
                        'title' => 'paid',
                    ],
                    [
                        'id' => 15,
                        'tag' => HospitalAccident::class,
                        'order' => '8',
                        'mode' => 'step',
                        'accident_status_id' => '7',
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

        $response2 = $this->get('/api/director/cases/' . $accident->id . '/scenario', $this->headers($this->getUser()));
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 8,
                        'tag' => HospitalAccident::class,
                        'order' => '1',
                        'mode' => 'step',
                        'accident_status_id' => '1',
                        'status' => 'visited',
                        'title' => 'new',
                    ],
                    [
                        'id' => 9,
                        'tag' => HospitalAccident::class,
                        'order' => '2',
                        'mode' => 'step',
                        'accident_status_id' => '8',
                        'status' => 'visited',
                        'title' => 'assistant_guarantee',
                    ],
                    [
                        'id' => 10,
                        'tag' => HospitalAccident::class,
                        'order' => '3',
                        'mode' => 'step',
                        'accident_status_id' => '9',
                        'status' => 'visited',
                        'title' => 'assigned',
                    ],
                    [
                        'id' => 11,
                        'tag' => HospitalAccident::class,
                        'order' => '4',
                        'mode' => 'step',
                        'accident_status_id' => '10',
                        'status' => 'visited',
                        'title' => 'hospital_guarantee',
                    ],
                    [
                        'id' => 12,
                        'tag' => HospitalAccident::class,
                        'order' => '5',
                        'mode' => 'step',
                        'accident_status_id' => '11',
                        'status' => 'visited',
                        'title' => 'hospital_invoice',
                    ],
                    [
                        'id' => 13,
                        'tag' => HospitalAccident::class,
                        'order' => '6',
                        'mode' => 'step',
                        'accident_status_id' => '12',
                        'status' => 'visited',
                        'title' => 'assistant_invoice',
                    ],
                    [
                        'id' => 14,
                        'tag' => HospitalAccident::class,
                        'order' => '7',
                        'mode' => 'step',
                        'accident_status_id' => '13',
                        'status' => 'visited',
                        'title' => 'paid',
                    ],
                    [
                        'id' => 15,
                        'tag' => HospitalAccident::class,
                        'order' => '8',
                        'mode' => 'step',
                        'accident_status_id' => '7',
                        'status' => 'current',
                        'title' => 'closed',
                    ],
                ],
        ]);
    }

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

        $response2 = $this->get('/api/director/cases/' . $accident->id . '/scenario', $this->headers($this->getUser()));
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 8,
                        'tag' => HospitalAccident::class,
                        'order' => '1',
                        'mode' => 'step',
                        'accident_status_id' => '1',
                        'status' => 'visited',
                        'title' => 'new',
                    ],
                    [
                        'id' => 9,
                        'tag' => HospitalAccident::class,
                        'order' => '2',
                        'mode' => 'step',
                        'accident_status_id' => '8',
                        'status' => '',
                        'title' => 'assistant_guarantee',
                    ],
                    [
                        'id' => 10,
                        'tag' => HospitalAccident::class,
                        'order' => '3',
                        'mode' => 'step',
                        'accident_status_id' => '9',
                        'status' => '',
                        'title' => 'assigned',
                    ],
                    [
                        'id' => 11,
                        'tag' => HospitalAccident::class,
                        'order' => '4',
                        'mode' => 'step',
                        'accident_status_id' => '10',
                        'status' => '',
                        'title' => 'hospital_guarantee',
                    ],
                    [
                        'id' => 12,
                        'tag' => HospitalAccident::class,
                        'order' => '5',
                        'mode' => 'step',
                        'accident_status_id' => '11',
                        'status' => '',
                        'title' => 'hospital_invoice',
                    ],
                    [
                        'id' => 13,
                        'tag' => HospitalAccident::class,
                        'order' => '6',
                        'mode' => 'step',
                        'accident_status_id' => '12',
                        'status' => '',
                        'title' => 'assistant_invoice',
                    ],
                    [
                        'id' => 14,
                        'tag' => HospitalAccident::class,
                        'order' => '7',
                        'mode' => 'step',
                        'accident_status_id' => '13',
                        'status' => '',
                        'title' => 'paid',
                    ],
                    [
                        'id' => 15,
                        'tag' => HospitalAccident::class,
                        'order' => '8',
                        'mode' => 'step',
                        'accident_status_id' => '7',
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
            'accident_status_id' => (new AccidentStatusesService())->getNewStatus(),
        ]);

        $accident->caseable->fill([
            'hospital_id' => factory(Hospital::class)->create()->id,
            'hospital_guarantee_id' => 0,
            'hospital_invoice_id' => 0,
        ])->save();

        (new AccidentService())->closeAccident($accident);

        $response2 = $this->get('/api/director/cases/' . $accident->id . '/scenario', $this->headers($this->getUser()));
        $response2->assertJson([
            'data' =>
                [
                    [
                        'id' => 8,
                        'tag' => HospitalAccident::class,
                        'order' => '1',
                        'mode' => 'step',
                        'accident_status_id' => '1',
                        'status' => 'visited',
                        'title' => 'new',
                    ],
                    [
                        'id' => 9,
                        'tag' => HospitalAccident::class,
                        'order' => '2',
                        'mode' => 'step',
                        'accident_status_id' => '8',
                        'status' => '',
                        'title' => 'assistant_guarantee',
                    ],
                    [
                        'id' => 10,
                        'tag' => HospitalAccident::class,
                        'order' => '3',
                        'mode' => 'step',
                        'accident_status_id' => '9',
                        'status' => 'visited',
                        'title' => 'assigned',
                    ],
                    [
                        'id' => 11,
                        'tag' => HospitalAccident::class,
                        'order' => '4',
                        'mode' => 'step',
                        'accident_status_id' => '10',
                        'status' => '',
                        'title' => 'hospital_guarantee',
                    ],
                    [
                        'id' => 12,
                        'tag' => HospitalAccident::class,
                        'order' => '5',
                        'mode' => 'step',
                        'accident_status_id' => '11',
                        'status' => '',
                        'title' => 'hospital_invoice',
                    ],
                    [
                        'id' => 13,
                        'tag' => HospitalAccident::class,
                        'order' => '6',
                        'mode' => 'step',
                        'accident_status_id' => '12',
                        'status' => '',
                        'title' => 'assistant_invoice',
                    ],
                    [
                        'id' => 14,
                        'tag' => HospitalAccident::class,
                        'order' => '7',
                        'mode' => 'step',
                        'accident_status_id' => '13',
                        'status' => 'visited',
                        'title' => 'paid',
                    ],
                    [
                        'id' => 15,
                        'tag' => HospitalAccident::class,
                        'order' => '8',
                        'mode' => 'step',
                        'accident_status_id' => '7',
                        'status' => 'current',
                        'title' => 'closed',
                    ],
                ],
        ]);
    }

    public function testDoctorCaseScenarioNew(): void
    {
        $accidentId = (new AccidentService)->create([
            'accident_status_id' => (new AccidentStatusesService())->getNewStatus(),
            'caseable_type' => DoctorAccident::class,
        ])->getAttribute('id');

        $response2 = $this->get('/api/director/cases/' . $accidentId . '/scenario', $this->headers($this->getUser()));
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => DoctorAccident::class,
                    'order' => '1',
                    'mode' => 'step',
                    'accident_status_id' => '1',
                    'status' => 'current',
                    'title' => 'new',
                ],
                [
                    'id' => 2,
                    'tag' => DoctorAccident::class,
                    'order' => '2',
                    'mode' => 'step',
                    'accident_status_id' => '2',
                    'status' => '',
                    'title' => 'assigned',
                ],
                [
                    'id' => 3,
                    'tag' => DoctorAccident::class,
                    'order' => '3',
                    'mode' => 'step',
                    'accident_status_id' => '3',
                    'status' => '',
                    'title' => 'in_progress',
                ],
                [
                    'id' => 4,
                    'tag' => DoctorAccident::class,
                    'order' => '4',
                    'mode' => 'step',
                    'accident_status_id' => '4',
                    'status' => '',
                    'title' => 'sent',
                ],
                [
                    'id' => 5,
                    'tag' => DoctorAccident::class,
                    'order' => '5',
                    'mode' => 'step',
                    'accident_status_id' => '5',
                    'status' => '',
                    'title' => 'paid',
                ],
                [
                    'id' => 7,
                    'tag' => DoctorAccident::class,
                    'order' => '7',
                    'mode' => 'step',
                    'accident_status_id' => '7',
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

        $response2 = $this->get('/api/director/cases/' . $accident->id . '/scenario', $this->headers($this->getUser()));
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => DoctorAccident::class,
                    'order' => '1',
                    'mode' => 'step',
                    'accident_status_id' => '1',
                    'status' => 'visited',
                    'title' => 'new',
                ],
                [
                    'id' => 2,
                    'tag' => DoctorAccident::class,
                    'order' => '2',
                    'mode' => 'step',
                    'accident_status_id' => '2',
                    'status' => 'visited',
                    'title' => 'assigned',
                ],
                [
                    'id' => 3,
                    'tag' => DoctorAccident::class,
                    'order' => '3',
                    'mode' => 'step',
                    'accident_status_id' => '3',
                    'status' => 'visited',
                    'title' => 'in_progress',
                ],
                [
                    'id' => 4,
                    'tag' => DoctorAccident::class,
                    'order' => '4',
                    'mode' => 'step',
                    'accident_status_id' => '4',
                    'status' => '',
                    'title' => 'sent',
                ],
                [
                    'id' => 5,
                    'tag' => DoctorAccident::class,
                    'order' => '5',
                    'mode' => 'step',
                    'accident_status_id' => '5',
                    'status' => '',
                    'title' => 'paid',
                ],
                [
                    'id' => 7,
                    'tag' => DoctorAccident::class,
                    'order' => '7',
                    'mode' => 'step',
                    'accident_status_id' => '7',
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
            'title' => AccidentStatusesService::STATUS_REJECT,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ]);
        self::assertEquals($accident->accident_status_id, $rejectStatus->id);

        $response2 = $this->get('/api/director/cases/' . $accident->id . '/scenario', $this->headers($this->getUser()));
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => DoctorAccident::class,
                    'order' => '1',
                    'mode' => 'step',
                    'accident_status_id' => '1',
                    'status' => 'visited',
                    'title' => 'new',
                ],
                [
                    'id' => 6,
                    'tag' => DoctorAccident::class,
                    'order' => '6',
                    'mode' => 'skip:doctor',
                    'accident_status_id' => '6',
                    'status' => 'current',
                    'title' => 'reject',
                ],
                [
                    'id' => 7,
                    'tag' => DoctorAccident::class,
                    'order' => '7',
                    'mode' => 'step',
                    'accident_status_id' => '7',
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
            'title' => AccidentStatusesService::STATUS_REJECT,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ]);
        self::assertEquals($accident->accident_status_id, $rejectStatus->id);

        // closing an accident
        $accidentService->closeAccident($accident);

        $response2 = $this->get('/api/director/cases/' . $accident->id . '/scenario', $this->headers($this->getUser()));
        $response2->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'tag' => DoctorAccident::class,
                    'order' => '1',
                    'mode' => 'step',
                    'accident_status_id' => '1',
                    'status' => 'visited',
                    'title' => 'new',
                ],
                [
                    'id' => 6,
                    'tag' => DoctorAccident::class,
                    'order' => '6',
                    'mode' => 'skip:doctor',
                    'accident_status_id' => '6',
                    'status' => 'visited',
                    'title' => 'reject',
                ],
                [
                    'id' => 7,
                    'tag' => DoctorAccident::class,
                    'order' => '7',
                    'mode' => 'step',
                    'accident_status_id' => '7',
                    'status' => 'current',
                    'title' => 'closed',
                ],
            ],
        ]);
    }
}
