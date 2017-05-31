<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;

use App\Accident;
use App\User;
use Tests\Feature\Api\JwtHeaders;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CasesTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;

    /**
     * @test
     *
     * Test: GET /api/authenticate.
     */
    public function it_authenticate_a_user()
    {
        $user = factory(User::class)->create(['password' => bcrypt('foo')]);

        $response = $this->json('POST', '/api/authenticate', ['email' => $user->email, 'password' => 'foo']);

        $response->assertStatus(200)->assertJsonStructure(['token']);
    }

    public function testIndex()
    {
        factory(Accident::class, 7)->create();
        $response = $this->get('/api/director/cases');

        $response->assertStatus(200)->assertJson([
            'data' => [
                ['id' => 1]
            ],
            "meta" => [
                "pagination" => [
                    "total" => 7,
                    "count" => 7,
                    "per_page" => 10,
                    "current_page" => 1,
                    "total_pages" => 1,
                    "links" => []
                ]
            ]
        ]);
    }

    public function testCreate()
    {
        $user = factory(User::class)->create(['password' => bcrypt('foo')]);

        $caseData = [

        ];


        $this->post('/api/director/cases', $caseData, $this->headers($user))->seeStatusCode(201);
    }

}
