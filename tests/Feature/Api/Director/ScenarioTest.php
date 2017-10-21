<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;

use App\Accident;
use App\Scenario;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;

class ScenarioTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testGetScenario ()
    {
        $seeder = new \ScenariosTableSeeder();
        $seeder->run();

        $response = $this->post('/api/director/cases', $caseData = [], $this->headers($this->getUser()));
        $response->assertStatus(201);
        $accident = $response->json()['accident'];

        $response2 = $this->get('/api/director/cases/' . $accident['id'] . '/scenario', $this->headers($this->getUser()));
        $response2->assertJson(
            [
                'data' => [
                    [
                        "accident_status_id" => "1",
                        "id" => 1,
                        "mode" => "step",
                        "order" => "1",
                        "status" => "current",
                        "tag" => "App\\DoctorAccident",
                        "title" => "new"
                    ],
                    [
                        "accident_status_id" => "2",
                        "id" => 2,
                        "mode" => "step",
                        "order" => "2",
                        "status"  => "",
                        "tag" => "App\\DoctorAccident",
                        "title" => "assigned"
                    ],
                    [
                        "accident_status_id"  => "3",
                        "id" => 3,
                        "mode" => "step",
                        "order" => "3",
                        "status" => "",
                        "tag" => "App\\DoctorAccident",
                        "title" => "in_progress"
                    ],
                    ["accident_status_id"=>"4","id"=>4,"mode"=>"step","order"=>"4","status"=>"","tag"=>"App\\DoctorAccident","title"=>"sent"],
                    ["accident_status_id"=>"5","id"=>5,"mode"=>"step","order"=>"5","status"=>"","tag"=>"App\\DoctorAccident","title"=>"paid"],
                    ["accident_status_id"=>"7","id"=>7,"mode"=>"step","order"=>"7","status"=>"","tag"=>"App\\DoctorAccident","title"=>"closed"],
                ],
            ]);
    }
}
