<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Import\Dhv24;

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
    }
}
