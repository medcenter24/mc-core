<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Doctor;

use App\Accident;
use App\Doctor;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccidentsTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        // allow all roles for the test (maybe in the future I'll mock it for work with roles)
        // but it could be another unit test for testing only rle access
        \Roles::shouldReceive('hasRole')
            ->andReturnUsing(function () {
                return true;
            });
    }

    public function testIndex()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/doctor/accidents');

        $response->assertStatus(200)->assertJson([]);
    }

    public function testList()
    {
        $user = factory(User::class)->create();
        $doctor = factory(Doctor::class)->create(['user_id' => $user->id]);

        $assistant = factory(Accident::class)->create([
            'caseable_id' => function () use ($doctor) {
                return factory(\App\DoctorAccident::class)->create([
                    'doctor_id' => $doctor->id
                ])->id;
            },
            'caseable_type' => \App\DoctorAccident::class,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/director/accidents/' . $assistant->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($assistant->toArray());
    }

    public function testShow()
    {
        $user = factory(User::class)->create();
        $doctor = factory(Doctor::class)->create(['user_id' => $user->id]);

        $assistant = factory(Accident::class)->create([
            'caseable_id' => function () use ($doctor) {
                return factory(\App\DoctorAccident::class)->create([
                    'doctor_id' => $doctor->id
                ])->id;
            },
            'caseable_type' => \App\DoctorAccident::class,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/director/accidents/' . $assistant->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($assistant->toArray());
    }

}
