<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Import\Dhv24;

use App\Accident;
use App\DoctorAccident;
use App\Services\Import\Dhv24\Dhv24Docx2017Provider;
use Tests\SamplePath;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Docx2017ProiderTest extends TestCase
{
    use DatabaseMigrations;
    use SamplePath;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testImportWithoutDoctor()
    {
        $provider = new Dhv24Docx2017Provider();
        $provider->load($this->getSampleFile('t1.docx'));
        self::assertTrue($provider->check(), 'File content is checked');
        $provider->import();
        $accident = $provider->getLastAccident();

        /** Accident data */
        self::assertEquals(1, $accident->id, 'Accident is stored');
        self::assertEquals(0, $accident->parent_id, 'Parent is 0');
        self::assertEquals(1, $accident->patient_id, 'Patient has been loaded');
        self::assertEquals(1, $accident->accident_type_id, 'Accident type selected');
        self::assertEquals(1, $accident->accident_status_id, 'Accident status selected');
        self::assertEquals(1, $accident->assistant_id, 'Assistant has been loaded');
        self::assertNotEquals('FakeAssistantRef', $accident->assistant_ref_num, 'Assistant referral number has been parsed');
        self::assertEquals(1, $accident->caseable_id, 'Caseable type is selected');
        self::assertEquals(DoctorAccident::class, $accident->caseable_type, 'Caseable type is DoctorAccident');
        self::assertNotEquals('Fake-import-num', $accident->ref_num, 'Accident Referral number is correct');
        self::assertNotEquals('FakeImport', $accident->title, 'Accident title is correct');
        self::assertNotEquals('FakeAddress', $accident->address, 'Address is presented');
        self::assertNotEquals('FakeContacts', $accident->contacts, 'Contacts is presented');
        self::assertNotEquals('FakeSymptoms', $accident->symptoms, 'Symptoms is presented');

        /** DoctorAccident data */
        $doctorAccident = $accident->caseable;
        self::assertEquals(1, $doctorAccident->id, 'Doctor Accident created and stored');
        self::assertEquals(0, $doctorAccident->doctor_id, 'Doctor is not provided');
        self::assertEquals(1, $doctorAccident->city_id, 'City exists');
        self::assertEquals(DoctorAccident::STATUS_CLOSED, $doctorAccident->status, 'Status is closed');
        self::assertNotEquals('FakeDiagnose', $doctorAccident->diagnose);
        self::assertNotEquals('FakeInvestigation', $doctorAccident->investigation);

        /** Patient */
        $patient = $accident->patient;
        self::assertEquals(1, $patient->id, 'Patient is loaded');
        self::assertEquals('Artamonov Timur', $patient->name, 'Patient has correct name');
    }

    public function testImportWithDoctor()
    {
        $provider = new Dhv24Docx2017Provider();
        $provider->load($this->getSampleFile('t2.docx'));
        self::assertTrue($provider->check(), 'File content is checked');
        $provider->import();
        $accident = $provider->getLastAccident();

        /** Accident data */
        self::assertEquals(1, $accident->id, 'Accident is stored');
        self::assertEquals(0, $accident->parent_id, 'Parent is 0');
        self::assertEquals(1, $accident->patient_id, 'Patient has been loaded');
        self::assertEquals(1, $accident->accident_type_id, 'Accident type selected');
        self::assertEquals(1, $accident->accident_status_id, 'Accident status selected');
        self::assertEquals(1, $accident->assistant_id, 'Assistant has been loaded');
        self::assertNotEquals('FakeAssistantRef', $accident->assistant_ref_num, 'Assistant referral number has been parsed');
        self::assertEquals(1, $accident->caseable_id, 'Caseable type is selected');
        self::assertEquals(DoctorAccident::class, $accident->caseable_type, 'Caseable type is DoctorAccident');
        self::assertNotEquals('Fake-import-num', $accident->ref_num, 'Accident Referral number is correct');
        self::assertNotEquals('FakeImport', $accident->title, 'Accident title is correct');
        self::assertNotEquals('FakeAddress', $accident->address, 'Address is presented');
        self::assertNotEquals('FakeContacts', $accident->contacts, 'Contacts is presented');
        self::assertNotEquals('FakeSymptoms', $accident->symptoms, 'Symptoms is presented');

        /** DoctorAccident data */
        $doctorAccident = $accident->caseable;
        self::assertEquals(1, $doctorAccident->id, 'Doctor Accident created and stored');
        self::assertEquals(1, $doctorAccident->doctor_id, 'Doctor is provided');
        self::assertEquals(1, $doctorAccident->city_id, 'City exists');
        self::assertEquals(DoctorAccident::STATUS_CLOSED, $doctorAccident->status, 'Status is closed');
        self::assertNotEquals('FakeDiagnose', $doctorAccident->diagnose);
        self::assertNotEquals('FakeInvestigation', $doctorAccident->investigation);

        /** Doctor */
        $doctor = $doctorAccident->doctor;
        self::assertEquals(1, $doctor->id, 'Doctor is loaded');
        self::assertEquals('Ralitsa Baharova', $doctor->name, 'Doctor name loaded');
        self::assertEquals('female', $doctor->gender, 'Doctor sex loaded');
        self::assertEquals('282870448', $doctor->medical_board_num, 'Doctor board loaded');

        /** Patient */
        $patient = $accident->patient;
        self::assertEquals(1, $patient->id, 'Patient is loaded');
        self::assertEquals('Zhukovich Nataliia', $patient->name, 'Patient has correct name');
    }

    public function testFosterAbigail()
    {
        // as I know that I need paren accident, so I need to create one:
        $parentAccident = factory(Accident::class)->create([
            'ref_num' => 'G000-0000-CDDNF'
        ]);

        $provider = new Dhv24Docx2017Provider();
        $provider->load($this->getSampleFile('FosterAbigail.DHV.docx'));
        self::assertTrue($provider->check(), 'File content is checked');
        $provider->import();
        $accident = $provider->getLastAccident();

        /** Accident data */
        self::assertEquals(2, $accident->id, 'Accident is stored');
        self::assertEquals($parentAccident->id, $accident->parent_id, 'Parent is matched');
        self::assertEquals(2, $accident->patient_id, 'Patient has been loaded');
        self::assertEquals(2, $accident->accident_type_id, 'Accident type selected');
        self::assertEquals(3, $accident->accident_status_id, 'Accident status selected');
        self::assertEquals(2, $accident->assistant_id, 'Assistant has been loaded');
        self::assertNotEquals('FakeAssistantRef', $accident->assistant_ref_num, 'Assistant referral number has been parsed');
        self::assertEquals(2, $accident->caseable_id, 'Caseable type is selected');
        self::assertEquals(DoctorAccident::class, $accident->caseable_type, 'Caseable type is DoctorAccident');
        self::assertNotEquals('Fake-import-num', $accident->ref_num, 'Accident Referral number is correct');
        self::assertNotEquals('FakeImport', $accident->title, 'Accident title is correct');
        self::assertNotEquals('FakeAddress', $accident->address, 'Address is presented');
        self::assertNotEquals('FakeContacts', $accident->contacts, 'Contacts is presented');
        self::assertNotEquals('FakeSymptoms', $accident->symptoms, 'Symptoms is presented');

        /** DoctorAccident data */
        /** @var DoctorAccident $doctorAccident */
        $doctorAccident = $accident->caseable;
        self::assertEquals(2, $doctorAccident->id, 'Doctor Accident created and stored');
        self::assertEquals(1, $doctorAccident->doctor_id, 'Doctor is provided');
        self::assertEquals(2, $doctorAccident->city_id, 'City exists');
        self::assertEquals('2017-01-01 16:30:00', $doctorAccident->visit_time, 'City exists');
        self::assertEquals(DoctorAccident::STATUS_CLOSED, $doctorAccident->status, 'Status is closed');
        self::assertNotEquals('FakeDiagnose', $doctorAccident->diagnose);
        self::assertNotEquals('FakeInvestigation', $doctorAccident->investigation);

        /** Surveable */
        self::assertEquals(1, $doctorAccident->surveable()->count(), 'Count of the surveable is matched');

        /** Diagnostics */
        self::assertEquals(2, $doctorAccident->diagnostics()->count(), 'Count of the diagnostics is matched');

        /** Doctor */
        $doctor = $doctorAccident->doctor;
        self::assertEquals(1, $doctor->id, 'Doctor is loaded');
        self::assertEquals('Eugeni Novikov', $doctor->name, 'Doctor name loaded');
        self::assertEquals('male', $doctor->gender, 'Doctor sex loaded');
        self::assertEquals('4305303', $doctor->medical_board_num, 'Doctor board loaded');

        /** Patient */
        $patient = $accident->patient;
        self::assertEquals(2, $patient->id, 'Patient is loaded');
        self::assertEquals('Foster Abigail', $patient->name, 'Patient has correct name');

        /** Services */
        self::assertEquals(22, $doctorAccident->services()->count(), 'Services were loaded');
    }
}
