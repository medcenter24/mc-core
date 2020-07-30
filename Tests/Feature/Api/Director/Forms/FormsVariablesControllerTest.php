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

declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Forms;

use medcenter24\mcCore\App\Services\Form\FormVariableService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class FormsVariablesControllerTest extends TestCase
{
    use DirectorTestTraitApi;

    public function testSearch(): void
    {
        $response = $this->sendPost('/api/director/forms/variables/search', []);
        $response->assertStatus(200);
        $response->assertJson(array(

            'data' =>
                array(
                    0 =>
                        array(
                            'title' => 'Accident Assistant Title',
                            'key' => ':accident.assistant.title',
                            'type' => 'accident',
                        ),
                    1 =>
                        array(
                            'title' => 'Accident Assistant Comment',
                            'key' => ':accident.assistant.comment',
                            'type' => 'accident',
                        ),
                    2 =>
                        array(
                            'title' => 'Accident Patient Name',
                            'key' => ':accident.patient.name',
                            'type' => 'accident',
                        ),
                    3 =>
                        array(
                            'title' => 'Accident Patient Birthday Date',
                            'key' => ':accident.patient.birthday.date',
                            'type' => 'accident',
                        ),
                    4 =>
                        array(
                            'title' => 'Accident Assistant_Ref_Num',
                            'key' => ':accident.assistant_ref_num',
                            'type' => 'accident',
                        ),
                    5 =>
                        array(
                            'title' => 'Accident Ref_Num',
                            'key' => ':accident.ref_num',
                            'type' => 'accident',
                        ),
                    6 =>
                        array(
                            'title' => 'Accident Symptoms',
                            'key' => ':accident.symptoms',
                            'type' => 'accident',
                        ),
                    7 =>
                        array(
                            'title' => 'Accident Caseable Investigation',
                            'key' => ':accident.caseable.investigation',
                            'type' => 'accident',
                        ),
                    8 =>
                        array(
                            'title' => 'Accident Caseable Recommendation',
                            'key' => ':accident.caseable.recommendation',
                            'type' => 'accident',
                        ),
                    9 =>
                        array(
                            'title' => 'Accident Parent_Id',
                            'key' => ':accident.parent_id',
                            'type' => 'accident',
                        ),
                    10 =>
                        array(
                            'title' => 'Accident Parent Ref_Num',
                            'key' => ':accident.parent.ref_num',
                            'type' => 'accident',
                        ),
                    11 =>
                        array(
                            'title' => 'Accident Caseable Doctor Name',
                            'key' => ':accident.caseable.doctor.name',
                            'type' => 'accident',
                        ),
                    12 =>
                        array(
                            'title' => 'Accident Caseable Doctor Medical_Board_Num',
                            'key' => ':accident.caseable.doctor.medical_board_num',
                            'type' => 'accident',
                        ),
                    13 =>
                        array(
                            'title' => 'Accident Caseable Hospital Title',
                            'key' => ':accident.caseable.hospital.title',
                            'type' => 'accident',
                        ),
                    14 =>
                        array(
                            'title' => 'Accident Income Currency Ico',
                            'key' => ':accident.income.currency.ico',
                            'type' => 'accident',
                        ),
                    15 =>
                        array(
                            'title' => 'Accident Caseable Diagnostics',
                            'key' => ':accident.caseable.diagnostics',
                            'type' => 'accident',
                        ),
                    16 =>
                        array(
                            'title' => 'Accident Caseable Surveys',
                            'key' => ':accident.caseable.surveys',
                            'type' => 'accident',
                        ),
                    17 =>
                        array(
                            'title' => 'Accident Caseable Services',
                            'key' => ':accident.caseable.services',
                            'type' => 'accident',
                        ),
                    18 =>
                        array(
                            'title' => 'Accident Income Currency Title',
                            'key' => ':accident.income.currency.title',
                            'type' => 'accident',
                        ),
                    19 =>
                        array(
                            'title' => 'Accident Income Value',
                            'key' => ':accident.income.value',
                            'type' => 'accident',
                        ),
                    20 =>
                        array(
                            'title' => 'Accident Caseable Visit_Time Time',
                            'key' => ':accident.caseable.visit_time.time',
                            'type' => 'accident',
                        ),
                    21 =>
                        array(
                            'title' => 'Accident Caseable Visit_Time Date',
                            'key' => ':accident.caseable.visit_time.date',
                            'type' => 'accident',
                        ),
                    22 =>
                        array(
                            'title' => 'Accident City Region Country Title',
                            'key' => ':accident.city.region.country.title',
                            'type' => 'accident',
                        ),
                    23 =>
                        array(
                            'title' => 'Accident City Region Title',
                            'key' => ':accident.city.region.title',
                            'type' => 'accident',
                        ),
                    24 =>
                        array(
                            'title' => 'Accident City Title',
                            'key' => ':accident.city.title',
                            'type' => 'accident',
                        ),
                    25 =>
                        array(
                            'title' => 'Accident Documents',
                            'key' => ':accident.documents',
                            'type' => 'accident',
                        ),
                ),
        ));
    }
}
