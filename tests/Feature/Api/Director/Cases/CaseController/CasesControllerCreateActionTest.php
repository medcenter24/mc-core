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
use App\Patient;
use App\Payment;
use App\Services\AccidentStatusesService;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CasesControllerCreateActionTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testCreateWithoutData()
    {
        $response = $this->post('/api/director/cases', [], $this->headers($this->getUser()));
        $response->assertStatus(201)->assertJson([
            'accident' => [
                'id' => 1,
                'createdBy' => 1,
                'parentId' => 0,
                'patientId' => 0,
                'accidentTypeId' => 0,
                'accidentStatusId' => 1,
                'assistantId' => 0,
                'caseableId' => 1,
                'cityId' => 0,
                'formReportId' => 0,
                'caseableType' => 'App\\HospitalAccident',
                'assistantRefNum' => '',
                'title' => '',
                'address' => '',
                'contacts' => '',
                'symptoms' => '',
                'deletedAt' => null,
                'closedAt' => null,
            ]
        ]);
    }

    /**
     * Creating a case but with the data for accident only (without dependencies and relations)
     */
    public function testCreateWithAccidentDataOnlyEmptyAllData()
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
        $response = $this->json('post', '/api/director/cases', $data, $this->headers($this->getUser()));

        $response->assertStatus(201)->assertJson([
            'accident' => [
                'id' => 1,
                'createdBy' => 1,
                'parentId' => 0,
                'patientId' => 0,
                'accidentTypeId' => 0,
                'accidentStatusId' => 1,
                'assistantId' => '0',
                'caseableId' => 1,
                'cityId' => 0,
                'formReportId' => 0,
                'caseableType' => 'App\\HospitalAccident',
                'assistantPaymentId' => 0,
                'incomePaymentId' => 0,
                'caseablePaymentId' => 0,
                'assistantRefNum' => '',
                'title' => '',
                'address' => '',
                'contacts' => '',
                'symptoms' => '',
                'deletedAt' => null,
                'closedAt' => null,
            ]
        ]);
    }

    /**
     * Creating a case but with the data for accident only (without dependencies and relations)
     */
    public function testCreateWithAccidentDataOnlyAccidentRequestRules()
    {
        $data = [
            'accident' => [
                'accidentStatusId' => "1",
                'accidentTypeId' => "1",
                'address' => "",
                'assistantId' => 2,
                'assistantRefNum' => "",
                'caseableId' => "3",
                'caseableType' => 'App\DoctorAccidents',
                'cityId' => 3,
                'closedAt' => null,
                'contacts' => "",
                'createdBy' => "2",
                'deletedAt' => null,
                'formReportId' => "7",
                'handlingTime' => "2018-11-16 02:46:00",
                'id' => 4,
                'income' => null,
                'parentId' => 3,
                'patientId' => 3,
                'refNum' => "test",
                'symptoms' => "",
                'title' => "",
                'assistantPaymentId' => 2,
                'incomePaymentId' => 5,
                'caseablePaymentId' => 8,
            ]
        ];
        $response = $this->json('post', '/api/director/cases', $data, $this->headers($this->getUser()));

        $content = $response->assertStatus(422)->getContent();
        $ans = json_decode($content);
        self::assertJson($ans->errors->accident[0]);
        self::assertSame([
            'parentId' => [
                'Parent is incorrect',
            ],
            'caseableType' => [
                'The selected caseable type is invalid.',
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
        ], json_decode($ans->errors->accident[0], 1));
    }

    /**
     * Creating a case but with the data for accident only (without dependencies and relations)
     */
    public function testCreateWithAccidentDataOnly4()
    {
        $data = [
            'accident' => [
                'accidentStatusId' => AccidentStatus::firstOrCreate([
                    'title' => AccidentStatusesService::STATUS_NEW,
                    'type' => AccidentStatusesService::TYPE_ACCIDENT,
                ])->id,
                'accidentTypeId' => factory(AccidentType::class)->create()->id,
                'address' => "any",
                'assistantId' => factory(Assistant::class)->create()->id,
                'assistantRefNum' => "ref---",
                'caseableId' => factory(DoctorAccident::class)->create()->id,
                'caseableType' => DoctorAccident::class,
                'cityId' => factory(City::class)->create()->id,
                'closedAt' => null,
                'contacts' => "anything",
                'deletedAt' => null,
                'formReportId' => factory(FormReport::class)->create()->id,
                'handlingTime' => "2018-11-16 02:46:00",
                'parentId' => factory(Accident::class)->create()->id,
                'patientId' => factory(Patient::class)->create()->id,
                'refNum' => "test",
                'symptoms' => "aaa",
                'title' => "ccc",
                'assistantPaymentId' => factory(Payment::class)->create()->id,
                'incomePaymentId' => factory(Payment::class)->create()->id,
                'caseablePaymentId' => factory(Payment::class)->create()->id,
            ]
        ];
        $response = $this->json('post', '/api/director/cases', $data, $this->headers($this->getUser()));

        $response->assertStatus(201)->assertJson([
            'accident' => [
                'id' => 2,
                'createdBy' => 2,
                'parentId' => 1,
                'patientId' => 0,
                'accidentTypeId' => 1,
                'accidentStatusId' => 1,
                'assistantId' => '1',
                'caseableId' => 3,
                'cityId' => 1,
                'formReportId' => 1,
                'caseableType' => 'App\\DoctorAccident',
                'assistantRefNum' => 'ref---',
                'title' => 'ccc',
                'address' => 'any',
                'contacts' => 'anything',
                'symptoms' => 'aaa',
                // will be updated on the creation and updating of the record and can't be manipulated
                // 'createdAt' => '2018-11-20 14:39:50',
                // 'updatedAt' => '2018-11-20 14:39:50',
                'deletedAt' => null,
                'closedAt' => null,
                'handlingTime' => '2018-11-16 02:46:00',
                'assistantPaymentId' => 1,
                'incomePaymentId' => 2,
                'caseablePaymentId' => 3,
            ]
        ]);
    }

    public function testCreateWithAccidentDataOnly5()
    {
        $data = [
            'accident' => [
                'accidentStatusId' => AccidentStatus::firstOrCreate([
                    'title' => AccidentStatusesService::STATUS_NEW,
                    'type' => AccidentStatusesService::TYPE_ACCIDENT,
                ])->id,
                'accidentTypeId' => factory(AccidentType::class)->create()->id,
                'address' => "any",
                'assistantId' => factory(Assistant::class)->create()->id,
                'assistantRefNum' => "ref---",
                'caseableId' => factory(DoctorAccident::class)->create()->id,
                'caseableType' => 'App\DoctorAccident',
                'cityId' => factory(City::class)->create()->id,
                'closedAt' => null,
                'contacts' => "anything",
                'deletedAt' => null,
                'formReportId' => factory(FormReport::class)->create()->id,
                'handlingTime' => "2018-11-16 02:46:00",
                'parentId' => factory(Accident::class)->create()->id,
                'patientId' => factory(Patient::class)->create()->id,
                'refNum' => "test",
                'symptoms' => "aaa",
                'title' => "ccc",
                'assistantPaymentId' => factory(Payment::class)->create()->id,
                'incomePaymentId' => factory(Payment::class)->create()->id,
                'caseablePaymentId' => factory(Payment::class)->create()->id,
            ]
        ];
        $response = $this->json('post', '/api/director/cases', $data, $this->headers($this->getUser()));

        $response->assertStatus(201)->assertJson([
            'accident' => [
                'id' => 2,
                'createdBy' => 2,
                'parentId' => 1,
                'patientId' => 0,
                'accidentTypeId' => 1,
                'accidentStatusId' => 1,
                'assistantId' => '1',
                'caseableId' => 3,
                'cityId' => 1,
                'formReportId' => 1,
                'caseableType' => 'App\\DoctorAccident',
                'assistantRefNum' => 'ref---',
                'title' => 'ccc',
                'address' => 'any',
                'contacts' => 'anything',
                'symptoms' => 'aaa',
                // will be updated on the creation and updating of the record and can't be manipulated
                // 'createdAt' => '2018-11-20 14:39:50',
                // 'updatedAt' => '2018-11-20 14:39:50',
                'deletedAt' => null,
                'closedAt' => null,
                'handlingTime' => '2018-11-16 02:46:00',
                'assistantPaymentId' => 1,
                'incomePaymentId' => 2,
                'caseablePaymentId' => 3,
            ]
        ]);
    }
}