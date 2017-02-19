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

    public function testIndex()
    {
        \Roles::shouldReceive('hasRole')
            ->times(1)
            ->andReturnUsing(function ($user, $role) {
                return true;
            });

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/accidentStatus');

        $response->assertStatus(200)
                ->assertJson([]);
    }
}
