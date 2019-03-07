<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director\Cases\CaseController;


use App\Accident;
use App\AccidentStatus;
use App\AccidentType;
use App\Assistant;
use App\City;
use App\DoctorAccident;
use App\FormReport;
use App\HospitalAccident;
use App\Patient;
use App\Payment;
use App\Services\AccidentStatusesService;
use App\Upload;
use App\User;
use Carbon\Carbon;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CasesControllerUpdateActionTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testUpdateWithoutData()
    {
        $accident = factory(Accident::class)->create([
            'accident_status_id' => factory(AccidentStatus::class)->create([
                'title' => 'anything'
            ])->id,
        ]);
        $response = $this->put('/api/director/cases/'.$accident->id, [], $this->headers($this->getUser()));
        $response->assertStatus(400)->assertJson([
            'message' => 'Accident data should be provided in the request data',
        ]);
    }

    /**
     * Creating a case but with the data for accident only (without dependencies and relations)
     */
    public function testUpdateWithAccidentDataEmptyAllData()
    {
        $accident = factory(Accident::class)->create([
            'accident_status_id' => factory(AccidentStatus::class)->create([
                'title' => 'anything'
            ])->id,
        ]);
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
        $response = $this->json('put', '/api/director/cases/'.$accident->id, $data, $this->headers($this->getUser()));

        $response->assertStatus(400)->assertJson([
            'message' => 'Accident data should be provided in the request data',
        ]);
    }

    /**
     * Creating a case but with the data for accident only (without dependencies and relations)
     */
    public function testUpdateWithAnotherAccident()
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
        $response = $this->json('put', '/api/director/cases/'.$accident->id, $data, $this->headers($this->getUser()));

        $response->assertStatus(400)->assertJson([
            'message' => 'Requested accident did not match to updated one',
        ]);
    }

    public function testUpdateWithCorrectAccidentButEmptyData()
    {
        $accident = factory(Accident::class)->create([
            'accident_status_id' => factory(AccidentStatus::class)->create([
                'title' => 'anything'
            ])->id,
        ]);
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
                'id' => $accident->id,
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
        $response = $this->json('put', '/api/director/cases/'.$accident->id, $data, $this->headers($this->getUser()));

        $response->assertStatus(200)->assertJson([
            'data' => [
                'accident' => [
                    'accidentStatusId' => '',
                    'accidentTypeId' => '',
                    'address' => '',
                    'assistantId' => '',
                    'assistantRefNum' => '',
                    'caseableId' => $accident->caseable_id . "",
                    'caseableType' => $accident->caseable_type . "",
                    'cityId' => '',
                    'closedAt' => $accident->closed_at ?: null,
                    'contacts' => '',
                    'createdAt' => $accident->created_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')),
                    'createdBy' => $accident->created_by . "",
                    'deletedAt' => $accident->deleted_at ? $accident->deleted_at->setTimezone($this->getUser()->timezone)->format(config('date.systemFormat')) : null,
                    'formReportId' => '',
                    'handlingTime' => null,
                    'id' => $accident->id,
                    'parentId' => '',
                    'patientId' => '',
                    'refNum' => '',
                    'symptoms' => '',
                    'title' => '',
                    // 'updatedAt' => $accident->updated_at, // needs to be updated by updater
                    'assistantPaymentId' => ($accident->assistant_payment_id ? : 0) . '',
                    'incomePaymentId' => ($accident->income_payment_id ?: 0) . '',
                    'caseablePaymentId' => ($accident->caseable_payment_id ?: 0) . '',
                ]
            ]
        ]);
    }

    public function testUpdateWithCorrectAccident()
    {
        $caseable = factory(DoctorAccident::class)->create();
        $accident = factory(Accident::class)->create([
            'created_by' => $createdBy = '' . factory(User::class)->create()->id,
            'accident_type_id' => $accidentType = factory(AccidentType::class)->create([
                'title' => \App\Services\AccidentTypeService::ALLOWED_TYPES[1],
            ])->id,
            'caseable_id' => $caseable->id,
            'caseable_type' => get_class($caseable),
            'assistant_guarantee_id' => factory(Upload::class)->create()->id,
        ]);

        $caseable2 = factory(HospitalAccident::class)->create();

        $data = [
            'accident' => [
                'accidentTypeId' => $accidentType = factory(AccidentType::class)->create([
                    'title' => \App\Services\AccidentTypeService::ALLOWED_TYPES[0]
                ])->id,
                'address' => $address = 'address string',
                'assistantId' => $assistant = factory(Assistant::class)->create()->id,
                'assistantRefNum' => $assistantRefNum = 'assistant-ref-num',
                'caseableId' => $caseable2->id,
                'caseableType' => get_class($caseable2),
                'cityId' => $city = factory(City::class)->create()->id,
                'contacts' => $contacts = 'contacts',
                'createdAt' => Carbon::create('2016', '02', '22', '22', '55', '55')->format('Y-m-d H:i:s'),
                'createdBy' => factory(User::class)->create()->id,
                'deletedAt' => date('Y-m-d H:i:s'),
                'formReportId' => $formReport = factory(FormReport::class)->create()->id,
                'handlingTime' => $handlingDate = Carbon::create('2016', '02', '22', '22', '55', '55')->format('Y-m-d H:i:s'),
                'id' => $accident->id,
                'parentId' => $parent = factory(Accident::class)->create()->id,
                'patientId' => $patient = factory(Patient::class)->create()->id,
                'refNum' => $accidentRefNum = 'ref num',
                'symptoms' => $accidentSymptoms = 'symptoms',
                'title' => $title = 'title',
                'updatedAt' => $updatedAt = Carbon::create('2016', '02', '22', '22', '55', '55')->format('Y-m-d H:i:s'),
                'assistantPaymentId' => factory(Payment::class)->create()->id,
                'incomePaymentId' => factory(Payment::class)->create()->id,
                'caseablePaymentId' => factory(Payment::class)->create()->id,
                'assistant_guarantee_id' => $assistantGuaranteeId = factory(Upload::class)->create()->id,
            ]
        ];
        $response = $this->json('put', '/api/director/cases/'.$accident->id, $data, $this->headers($this->getUser()));

        $response->assertStatus(200)->assertJson([
            'data' => [
                'accident' => [
                    'accidentTypeId' => $accidentType,
                    'address' => $address,
                    'assistantId' => $assistant,
                    'assistantRefNum' => $assistantRefNum,
                    'caseableId' => $caseable->id . '',
                    'caseableType' => get_class($caseable),
                    'cityId' => $city,
                    'contacts' => $contacts,
                    'createdAt' => $accident->created_at->format('Y-m-d H:i:s'),
                    'createdBy' => $createdBy,
                    'deletedAt' => null,
                    'formReportId' => $formReport,
                    'handlingTime' => $handlingDate,
                    'id' => $accident->id,
                    'parentId' => $parent,
                    'patientId' => $patient,
                    'refNum' => $accidentRefNum,
                    'symptoms' => $accidentSymptoms,
                    'title' => $title,
                    // 'updatedAt' => $accident->updated_at, // needs to be updated by updater
                    'assistantPaymentId' => ($accident->assistant_payment_id ? : 0) . '',
                    'incomePaymentId' => ($accident->income_payment_id ?: 0) . '',
                    'caseablePaymentId' => ($accident->caseable_payment_id ?: 0) . '',
                    'assistantGuaranteeId' => $assistantGuaranteeId,
                ]
            ]
        ]);

        self::assertNotEquals($updatedAt, $response->json('data.accident.updatedAt'));
    }

    /**
     * Check that if I sent incorrect data to the relation it won't be saved
     */
    public function testUpdateWithNonExistingRelations()
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
        $response = $this->json('put', '/api/director/cases/'.$accident->id, $data, $this->headers($this->getUser()));

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
        ], json_decode($ans->errors->accident[0], 1));
    }

    public function testClosedAccident(){
        $accident = factory(Accident::class)->create(['accident_status_id' => 0]);
        $data = [
            'accident' => [
                'id' => $accident->id,
            ]
        ];

        $accidentStatusService = new AccidentStatusesService();
        // closing an accident
        $accidentStatusService->closeAccident($accident);

        $response = $this->json('put', '/api/director/cases/'.$accident->id, $data, $this->headers($this->getUser()));

        $response->assertStatus(403)->assertJson([
            'message' => 'Already closed',
        ]);
    }

    public function testDeletedAccident(){
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
        $response = $this->json('put', '/api/director/cases/'.$accident->id, $data, $this->headers($this->getUser()));

        $response->assertStatus(403)->assertJson([
            'message' => 'Accident not found',
        ]);
    }
}