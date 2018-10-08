<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;

class ScenarioTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testGetScenario()
    {
        $seeder = new \ScenariosTableSeeder();
        $seeder->run();

        $response = $this->post('/api/director/cases', $caseData = [], $this->headers($this->getUser()));
        $response->assertStatus(201);
        $accident = $response->json()['accident'];

        $response2 = $this->get('/api/director/cases/' . $accident['id'] . '/scenario',
            $this->headers($this->getUser()));
        $response2 = $this->get('/api/director/cases/' . $accident['id'] . '/scenario',
            $this->headers($this->getUser()));
        $response2->assertJson(
            [
                'data' =>
                    array(
                        0 =>
                            array(
                                'id' => 8,
                                'tag' => 'App\\HospitalAccident',
                                'order' => '1',
                                'mode' => 'step',
                                'accident_status_id' => '1',
                                'status' => 'current',
                                'title' => 'new',
                            ),
                        1 =>
                            array(
                                'id' => 9,
                                'tag' => 'App\\HospitalAccident',
                                'order' => '2',
                                'mode' => 'step',
                                'accident_status_id' => '8',
                                'status' => '',
                                'title' => 'hospital_guarantee',
                            ),
                        2 =>
                            array(
                                'id' => 10,
                                'tag' => 'App\\HospitalAccident',
                                'order' => '3',
                                'mode' => 'step',
                                'accident_status_id' => '9',
                                'status' => '',
                                'title' => 'hospital_invoice',
                            ),
                        3 =>
                            array(
                                'id' => 11,
                                'tag' => 'App\\HospitalAccident',
                                'order' => '4',
                                'mode' => 'step',
                                'accident_status_id' => '10',
                                'status' => '',
                                'title' => 'assistant_invoice',
                            ),
                        4 =>
                            array(
                                'id' => 12,
                                'tag' => 'App\\HospitalAccident',
                                'order' => '5',
                                'mode' => 'step',
                                'accident_status_id' => '11',
                                'status' => '',
                                'title' => 'assistant_guarantee',
                            ),
                        5 =>
                            array(
                                'id' => 13,
                                'tag' => 'App\\HospitalAccident',
                                'order' => '6',
                                'mode' => 'step',
                                'accident_status_id' => '12',
                                'status' => '',
                                'title' => 'paid',
                            ),
                        6 =>
                            array(
                                'id' => 14,
                                'tag' => 'App\\HospitalAccident',
                                'order' => '7',
                                'mode' => 'step',
                                'accident_status_id' => '7',
                                'status' => '',
                                'title' => 'closed',
                            ),
                    ),
            ]);
    }
}
