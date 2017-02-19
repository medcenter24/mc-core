<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccidentStatusTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;

    protected function setUp()
    {
        parent::setUp();

        \Roles::shouldReceive('hasRole')
            ->times(1)
            ->andReturnUsing(function ($user, $role) {
                return true;
            });
    }

    public function testIndex()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/accidentStatus');

        $response->assertStatus(200)
                ->assertJson([]);
    }

    public function testStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/accidentStatus', ['_token' => csrf_token()]);

        dd($response->getContent());

        $response->assertStatus(200)
            ->assertJson([]);
    }
}
