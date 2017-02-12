<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Feature\Admin;

use App\Role;
use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Prophecy\Argument;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccessTest extends TestCase
{

    public function testMain()
    {
        \Roles::shouldReceive('hasRole')->times(0);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/');

        $response->assertStatus(200);
    }

    public function testAdmin()
    {
        \Roles::shouldReceive('hasRole')
            ->times(3)
            ->andReturnUsing(function ($user, $role) {
                return true;
            });

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/admin');

        $response->assertStatus(200);
    }

    public function testDoctor()
    {
        \Roles::shouldReceive('hasRole')
            ->times(1)
            ->andReturnUsing(function ($user, $role) {
                return false;
            });

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/admin');

        $response->assertStatus(403);
    }

    public function testDirector()
    {
        \Roles::shouldReceive('hasRole')
            ->times(1)
            ->andReturnUsing(function ($user, $role) {
                return false;
            });

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/admin');

        $response->assertStatus(403);
    }
}
