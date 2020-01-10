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
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\Tests\Unit\Services\Forms;

use Carbon\Carbon;
use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Diagnostic;
use medcenter24\mcCore\App\Doctor;
use medcenter24\mcCore\App\DoctorAccident;
use medcenter24\mcCore\App\DoctorService;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\FinanceCurrency;
use medcenter24\mcCore\App\Form;
use medcenter24\mcCore\App\Hospital;
use medcenter24\mcCore\App\HospitalAccident;
use medcenter24\mcCore\App\Patient;
use medcenter24\mcCore\App\Payment;
use medcenter24\mcCore\App\Services\Form\FormVariableService;
use medcenter24\mcCore\App\Services\FormService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\Tests\TestCase;

class FormServiceTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @var FormService
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FormService();
    }

    /**
     * @throws InconsistentDataException
     */
    public function testRefNumVar(): void
    {
        $accident = new Accident(['ref_num' => 'ooo']);
        $form = new Form([
            'template' => '<b>'.FormVariableService::VAR_ACCIDENT_REF_NUM.'</b>',
            'formable_type' => Accident::class,
        ]);
        self::assertSame('<b>ooo</b>', $this->service->getHtml($form, $accident));
    }

    /**
     * @throws InconsistentDataException
     */
    public function testVisitDate(): void
    {
        $doctorAccident = factory(DoctorAccident::class)->create([
            'visit_time' => Carbon::create('2019', '04', '03', '14', '59', '47'),
        ]);
        $accident = new Accident(['caseable_id' => $doctorAccident->id, 'caseable_type' => DoctorAccident::class]);
        $form = new Form([
            'template' => 'date: ' . FormVariableService::VAR_ACCIDENT_CASEABLE_VISIT_TIME_DATE
                . ' time: ' . FormVariableService::VAR_ACCIDENT_CASEABLE_VISIT_TIME_TIME,
            'formable_type' => Accident::class,
        ]);
        self::assertSame('date: 03.04.2019 time: 14:59', $this->service->getHtml($form, $accident));
    }



    /**
     * @throws InconsistentDataException
     */
    public function testParentData(): void
    {
        $parentAccident = factory(Accident::class)->create([
            'ref_num' => 'parent_ref_num'
        ]);
        $accident = new Accident(['parent_id' => $parentAccident->id]);
        $form = new Form([
            'template' => FormVariableService::VAR_ACCIDENT_PARENT_ID . ', ' . FormVariableService::VAR_ACCIDENT_PARENT_REF_NUM,
            'formable_type' => Accident::class,
        ]);
        self::assertSame('1, parent_ref_num', $this->service->getHtml($form, $accident));
    }

    /**
     * @throws InconsistentDataException
     */
    public function testHospitalData(): void
    {
        $hospital = factory(Hospital::class)->create([
            'title' => 'Hospital Name'
        ]);
        $hospitalAccident = factory(HospitalAccident::class)->create([
            'hospital_id' => $hospital->getAttribute('id'),
        ]);
        $accident = new Accident(['caseable_id' => $hospitalAccident->id, 'caseable_type' => HospitalAccident::class]);
        $form = new Form([
            'template' => FormVariableService::VAR_ACCIDENT_CASEABLE_HOSPITAL_TITLE,
            'formable_type' => Accident::class,
        ]);
        self::assertSame('Hospital Name', $this->service->getHtml($form, $accident));
    }

    /**
     * @throws InconsistentDataException
     */
    public function testIncomeCurrencyData(): void
    {
        $currency = factory(FinanceCurrency::class)->create([
            'title' => 'CurrName',
            'ico' => 'CurrIco'
        ]);
        $income = factory(Payment::class)->create([
            'currency_id' => $currency->getAttribute('id'),
            'value' => 10,
        ]);
        $accident = new Accident([
            'income_payment_id' => $income->getAttribute('id'),
        ]);
        $form = new Form([
            'template' => FormVariableService::VAR_ACCIDENT_INCOME_CURRENCY_ICO
                . ' ' . FormVariableService::VAR_ACCIDENT_INCOME_CURRENCY_TITLE
                . ' ' . FormVariableService::VAR_ACCIDENT_INCOME_VALUE,
            'formable_type' => Accident::class,
        ]);
        self::assertSame('CurrIco CurrName 10', $this->service->getHtml($form, $accident));
    }

    /**
     * @throws InconsistentDataException
     */
    public function testPatientData(): void
    {
        $patient = factory(Patient::class)->create([
            'name' => 'Patient Full Name',
            'birthday' => '2017-03-01',
        ]);

        $accident = factory(Accident::class)->create([
            'patient_id' => $patient->id,
        ]);

        $form = new Form([
            'template' => FormVariableService::VAR_ACCIDENT_PATIENT_NAME . ', ' . FormVariableService::VAR_ACCIDENT_PATIENT_BIRTHDAY,
            'formable_type' => Accident::class,
        ]);

        self::assertSame('Patient Full Name, 01.03.2017', $this->service->getHtml($form, $accident));
    }

    /**
     * @throws InconsistentDataException
     */
    public function testWithIfConditionTrue(): void
    {
        $parent = factory(Accident::class)->create([
            'ref_num' => '000-ref-num'
        ]);

        $accident = factory(Accident::class)->create([
            'parent_id' => $parent->id,
        ]);

        $form = new Form([
            'template' => 'covered by text <span '.FormService::CONDITION_IF.'="'
                .FormVariableService::VAR_ACCIDENT_PARENT_ID.'">'
                . FormVariableService::VAR_ACCIDENT_PARENT_REF_NUM
                . '</span> after if',
            'formable_type' => Accident::class,
        ]);

        // if true
        self::assertSame('covered by text <span>000-ref-num</span> after if', $this->service->getHtml($form, $accident));
    }

    /**
     * @throws InconsistentDataException
     */
    public function testWithIfRecursiveConditionTrue(): void
    {
        $parent = factory(Accident::class)->create([
            'ref_num' => '000-ref-num'
        ]);

        $accident = factory(Accident::class)->create([
            'parent_id' => $parent->id,
        ]);

        $form = new Form([
            'template' => 'covered by text <span '.FormService::CONDITION_IF.'="'
                .FormVariableService::VAR_ACCIDENT_PARENT_ID.'">'
                . FormVariableService::VAR_ACCIDENT_PARENT_REF_NUM
                    . ' <span '.FormService::CONDITION_IF.'="'
                        . FormVariableService::VAR_ACCIDENT_PARENT_ID.'"> '
                        . FormVariableService::VAR_ACCIDENT_PARENT_ID
                    . '</span>'
                    . ' after first '
                . '</span> after if',
            'formable_type' => Accident::class,
        ]);

        // if true
        self::assertSame(
            'covered by text <span>000-ref-num <span> 1</span> after first </span> after if',
            $this->service->getHtml($form, $accident)
        );
    }

    /**
     * @throws InconsistentDataException
     */
    public function testWithIfConditionFalse(): void
    {
        $accident = factory(Accident::class)->create([
            'parent_id' => 0,
        ]);

        $form = new Form([
            'template' => 'covered by text <span :template.if="'.FormVariableService::VAR_ACCIDENT_PARENT_ID.'">'
                . FormVariableService::VAR_ACCIDENT_PARENT_REF_NUM
                . '</span> after if',
            'formable_type' => Accident::class,
        ]);
        // if false
        self::assertSame('covered by text after if', $this->service->getHtml($form, $accident));
    }

    /**
     * @throws InconsistentDataException
     */
    public function testWithIfRecursiveConditionFalse(): void
    {
        $accident = factory(Accident::class)->create([
            'parent_id' => 0,
        ]);

        $form = new Form([
            'template' => 'covered by text <span '.FormService::CONDITION_IF.'="'
                .FormVariableService::VAR_ACCIDENT_PARENT_ID.'">'
                . FormVariableService::VAR_ACCIDENT_PARENT_REF_NUM
                    . '<span '
                        . FormService::CONDITION_IF
                        . '="'.FormVariableService::VAR_ACCIDENT_PARENT_ID.'"'
                    .'></span>'
                . '</span> after if',
            'formable_type' => Accident::class,
        ]);
        // if false
        self::assertSame('covered by text after if', $this->service->getHtml($form, $accident));
    }

    /**
     * No records in the storage for the form
     * @throws InconsistentDataException
     */
    public function testForConditionFalse(): void
    {
        $accident = factory(Accident::class)->create([
            'parent_id' => 0,
        ]);

        $form = new Form([
            'template' => 'covered by text <span '.FormService::CONDITION_FOR.'="'
                .FormVariableService::VAR_ACCIDENT_SERVICES.'">'
                . FormService::CONDITION_FOR_RESOURCE .'.id'
                . FormService::CONDITION_FOR_RESOURCE .'.title'
                . '</span> after if',
            'formable_type' => Accident::class,
        ]);
        // if false
        self::assertSame('covered by text after if', $this->service->getHtml($form, $accident));
    }

    /**
     * @throws InconsistentDataException
     */
    public function testForConditionTrue(): void
    {
        $accident = factory(Accident::class)->create();
        $accident->services()->detach();

        factory(DoctorService::class)->create([
            'title' => 'service 1',
        ]);
        factory(DoctorService::class)->create([
            'title' => 'service 2',
        ]);
        factory(DoctorService::class)->create([
            'title' => 'service 3',
        ]);
        $accident->services()->attach([1, 2, 3]);

        $form = new Form([
            'template' => 'covered by text <span :template.for="'
                . FormVariableService::VAR_ACCIDENT_SERVICES.'">'
                . FormService::CONDITION_FOR_RESOURCE .'.id, '
                . FormService::CONDITION_FOR_RESOURCE .'.title; '
                . '</span> after if',
            'formable_type' => Accident::class,
        ]);
        // if false
        self::assertSame(
            'covered by text <span><span>1, service 1; </span><span>2, service 2; </span><span>3, service 3; </span></span> after if',
            $this->service->getHtml($form, $accident)
        );
    }

    /**
     * @throws InconsistentDataException
     */
    public function testForConditionDiagnostics(): void
    {
        $accident = factory(Accident::class)->create();
        $accident->diagnostics()->detach();

        factory(Diagnostic::class)->create([
            'title' => 'd 1',
        ]);
        factory(Diagnostic::class)->create([
            'title' => 'd 2',
        ]);
        factory(Diagnostic::class)->create([
            'title' => 'd 3',
        ]);
        $accident->diagnostics()->attach([1, 2, 3]);

        $form = new Form([
            'template' => 'covered by text <span '.FormService::CONDITION_FOR.'="'
                . FormVariableService::VAR_ACCIDENT_DIAGNOSTICS.'">'
                . FormService::CONDITION_FOR_RESOURCE .'.title; '
                . '</span> after if',
            'formable_type' => Accident::class,
        ]);
        // if false
        self::assertSame(
            'covered by text <span><span>d 1; </span><span>d 2; </span><span>d 3; </span></span> after if',
            $this->service->getHtml($form, $accident)
        );
    }

    /**
     * @throws InconsistentDataException
     */
    public function testForConditionDiagnostics2(): void
    {
        $accident = factory(Accident::class)->create();
        $accident->diagnostics()->detach();

        factory(Diagnostic::class)->create([
            'title' => 'd 1',
        ]);
        factory(Diagnostic::class)->create([
            'title' => 'd 2',
        ]);
        factory(Diagnostic::class)->create([
            'title' => 'd 3',
        ]);
        $accident->diagnostics()->attach([1, 2, 3]);

        $form = new Form([
            'template' => '<span :template.for=":accident.diagnostics">
                            <div>:template.for.resource.title</div>
                        </span>',
            'formable_type' => Accident::class,
        ]);
        // if false
        self::assertSame(
            '<span><span>
       <div>d 1</div>
      </span><span>
       <div>d 2</div>
      </span><span>
       <div>d 3</div>
      </span></span>',
            $this->service->getHtml($form, $accident)
        );
    }

    /**
     * @throws InconsistentDataException
     */
    public function testParenData(): void
    {
        $patient = factory(Patient::class)->create([
            'name' => 'Patient Full Name',
            'birthday' => '2017-03-01',
        ]);

        $accident = factory(Accident::class)->create([
            'patient_id' => $patient->id,
        ]);

        $form = new Form([
            'template' => FormVariableService::VAR_ACCIDENT_PATIENT_NAME . ', ' . FormVariableService::VAR_ACCIDENT_PATIENT_BIRTHDAY,
            'formable_type' => Accident::class,
        ]);

        self::assertSame('Patient Full Name, 01.03.2017', $this->service->getHtml($form, $accident));
    }

    public function testHtmlDocCase(): void
    {
        $doctorAccident = factory(Accident::class)->create([
            'ref_num' => 'aaa-aaa-aaa',
            'caseable_type' => DoctorAccident::class,
            'caseable_id' => factory(DoctorAccident::class)->create([
                'doctor_id' => factory(Doctor::class)->create(['name' => 'Doctor Name'])->id,
            ])->id,
            'patient_id' => factory(Patient::class)->create([
                'name' => 'Patient Name'
            ])->id,
        ]);

        $form = factory(Form::class)->create([
            'title' => 'Form_1',
            'description' => 'Unit Test Form #1',
            'template' => '
                <p>Doctor: <b>'.FormVariableService::VAR_ACCIDENT_CASEABLE_DOCTOR_NAME.'</b></p>
                <p>Patient "'.FormVariableService::VAR_ACCIDENT_PATIENT_NAME
                .'" Doctor one more time '.
                FormVariableService::VAR_ACCIDENT_CASEABLE_DOCTOR_NAME
                .'. Current company is Medical Company.</p>
                <p>Ref number №'.FormVariableService::VAR_ACCIDENT_REF_NUM.'</p>',
        ]);

        self::assertSame('
    <p>Doctor: <b>Doctor Name</b></p>
    <p>Patient "Patient Name" Doctor one more time Doctor Name. Current company is Medical Company.</p>
    <p>Ref number №aaa-aaa-aaa</p>',
            $this->service->getHtml($form, $doctorAccident));
    }

    public function testHtmlHospCase(): void
    {
        $hospitalAccident = factory(Accident::class)->create([
            'ref_num' => 'aaa-aaa-aaa',
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => factory(HospitalAccident::class)->create([
                'hospital_id' => factory(Hospital::class)->create(['title' => 'Hospital Title'])->id,
            ])->id,
            'patient_id' => factory(Patient::class)->create([
                'name' => 'Patient Name'
            ])->id,
        ]);

        $form = factory(Form::class)->create([
            'title' => 'Form_1',
            'description' => 'Unit Test Form #1',
            'template' => '
                <p>Hospital: <b>'.FormVariableService::VAR_ACCIDENT_CASEABLE_HOSPITAL_TITLE.'</b></p>
                <p>Patient "'.FormVariableService::VAR_ACCIDENT_PATIENT_NAME
                .'" Hospital one more time '.FormVariableService::VAR_ACCIDENT_CASEABLE_HOSPITAL_TITLE
                .' and one more '.
                FormVariableService::VAR_ACCIDENT_CASEABLE_HOSPITAL_TITLE
                .'. Current company is Medical Company.</p>
                <p>Ref number №'.FormVariableService::VAR_ACCIDENT_REF_NUM.'</p>',
        ]);

        self::assertEquals('
    <p>Hospital: <b>Hospital Title</b></p>
    <p>Patient "Patient Name" Hospital one more time Hospital Title and one more Hospital Title. Current company is Medical Company.</p>
    <p>Ref number №aaa-aaa-aaa</p>', $this->service->getHtml($form, $hospitalAccident));
    }
}
