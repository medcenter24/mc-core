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

namespace Tests\Unit\Services;

use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Doctor;
use medcenter24\mcCore\App\DoctorAccident;
use medcenter24\mcCore\App\Form;
use medcenter24\mcCore\App\Hospital;
use medcenter24\mcCore\App\HospitalAccident;
use medcenter24\mcCore\App\Patient;
use medcenter24\mcCore\App\Services\FormService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

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

    public function testHtmlDocCase()
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
            'variables' => '[":doctor.name",":patient.name",":doctor.name",":ref.number"]',
            'template' => '
                <p>Doctor: <b>:doctor.name</b></p>
                <p>Patient ":patient.name" Doctor one more time :doctor.name. Current company is Medical Company.</p>
                <p>Ref number №:ref.number</p>',
        ]);

        self::assertEquals('
                <p>Doctor: <b>Doctor Name</b></p>
                <p>Patient "Patient Name" Doctor one more time Doctor Name. Current company is Medical Company.</p>
                <p>Ref number №aaa-aaa-aaa</p>', $this->service->getHtml($form, $doctorAccident));
    }

    public function testHtmlHospCase()
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
            'variables' => '[":hospital.title",":patient.name",":hospital.title",":hospital.title",":ref.number"]',
            'template' => '
                <p>Hospital: <b>:hospital.title</b></p>
                <p>Patient ":patient.name" Hospital one more time :hospital.title and one more :hospital.title. Current company is Medical Company.</p>
                <p>Ref number №:ref.number</p>',
        ]);

        self::assertEquals('
                <p>Hospital: <b>Hospital Title</b></p>
                <p>Patient "Patient Name" Hospital one more time Hospital Title and one more Hospital Title. Current company is Medical Company.</p>
                <p>Ref number №aaa-aaa-aaa</p>', $this->service->getHtml($form, $hospitalAccident));
    }
}
