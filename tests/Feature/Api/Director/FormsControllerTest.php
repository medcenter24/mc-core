<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;


use App\Accident;
use App\Doctor;
use App\DoctorAccident;
use App\Form;
use App\Patient;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;

class FormsControllerTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testStoreError()
    {
        $form = factory(Form::class)->create([
            'title' => 'Form_1',
            'description' => 'Unit Test Form #1',
            'variables' => '[":doctor.name",":patient.name",":doctor.name",":ref.number"]',
            'template' => '<p>Doctor: <b>:doctor.name</b></p>
                <p>Patient ":patient.name" Doctor one more time :doctor.name. Current company is Medical Company.</p>
                <p>Ref number №:ref.number</p>',
        ]);

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

        $response = $this->get('/api/director/forms/'.$form->id.'/'.$doctorAccident->id.'/html', $this->headers($this->getUser()));
        $response->assertStatus(200)->assertJson([
            'data' => '<p>Doctor: <b>Doctor Name</b></p>
                <p>Patient "Patient Name" Doctor one more time Doctor Name. Current company is Medical Company.</p>
                <p>Ref number №aaa-aaa-aaa</p>',
        ]);
    }
}
