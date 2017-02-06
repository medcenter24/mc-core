<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Feature\Admin;

use App\User;
use Prophecy\Argument;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccessTest extends TestCase
{

    private $admin;

    public function setUp()
    {
        parent::setUp();

        $user = $this->prophesize(User::class);

        $user->hasRole(Argument::type('string'))
            ->will(function ($args) {
                return $args[0] == 'admin';
            });

        $this->admin = $user->reveal();
    }

    public function testMain()
    {
        $response = $this
            ->actingAs($this->admin)
            ->get('/');

        $response->assertStatus(200);
    }

    public function testAdmin()
    {
        $response = $this
            ->actingAs($this->admin)
            ->get('/admin');

        $response->assertStatus(200);
    }

    public function testDoctor()
    {
        $response = $this
            ->actingAs($this->admin)
            ->get('/doctor');

        $response->assertStatus(403);
    }

    public function testDirector()
    {
        $response = $this
            ->actingAs($this->admin)
            ->get('/director');

        $response->assertStatus(403);
    }
}
