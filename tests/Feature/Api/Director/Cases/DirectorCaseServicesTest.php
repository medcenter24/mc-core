<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;

use App\Accident;
use App\DoctorAccident;
use App\DoctorService;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectorCaseServicesTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testGetNoServices()
    {
        $case = factory(Accident::class)->create();
        $response = $this->get('/api/director/cases/' . $case->id .'/services', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson(['data' => []]);
    }

    public function testGetServices()
    {
        $caseable = factory(DoctorAccident::class)->create();

        $accident = factory(Accident::class)->create();
        $accident->caseable_id = $caseable->id;
        $accident->caseable_type = DoctorAccident::class;
        $accident->save();

        $services = factory(DoctorService::class, 5)->create();
        $accident->caseable->services()->attach($services);
        self::assertEquals(5, $accident->caseable->services->count());

        $response = $this->get('/api/director/cases/' . $accident->id .'/services', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson(['data' => [[], [], [], [], []]]);
    }

}
