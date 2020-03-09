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

namespace medcenter24\mcCore\Tests\Feature\Api\Director;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Form;
use medcenter24\mcCore\App\Entity\Patient;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\App\Services\Form\FormVariableService;
use medcenter24\mcCore\Tests\Feature\Api\JwtHeaders;
use medcenter24\mcCore\Tests\Feature\Api\LoggedUser;
use medcenter24\mcCore\Tests\TestCase;

class FormsControllerTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testStoreError(): void
    {
        $form = factory(Form::class)->create([
            'title' => 'Form_1',
            'description' => 'Unit Test Form #1',
            'template' => '<p>Doctor: <b>'.FormVariableService::VAR_ACCIDENT_CASEABLE_DOCTOR_NAME.'</b></p>
                <p>Patient "'.FormVariableService::VAR_ACCIDENT_PATIENT_NAME
                . '" Doctor one more time '.FormVariableService::VAR_ACCIDENT_CASEABLE_DOCTOR_NAME
                . '. Current company is Medical Company.</p>
                <p>Ref number №'.FormVariableService::VAR_ACCIDENT_REF_NUM.'</p>',
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
