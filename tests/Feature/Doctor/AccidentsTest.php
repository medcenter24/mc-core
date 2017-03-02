<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Doctor;

use App\Accident;
use App\Doctor;
use App\DoctorAccident;
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

        $accident = factory(Accident::class)->create([
            'caseable_id' => function () use ($doctor) {
                return factory(DoctorAccident::class)->create([
                    'doctor_id' => $doctor->id
                ])->id;
            },
            'caseable_type' => DoctorAccident::class,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/doctor/accidents/', ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson([['doctor_id' => $doctor->id]]);
    }

    public function testShow()
    {
        $user = factory(User::class)->create();
        $doctor = factory(Doctor::class)->create(['user_id' => $user->id]);

        $accident = factory(Accident::class)->create([
            'caseable_id' => function () use ($doctor) {
                return factory(DoctorAccident::class)->create([
                    'doctor_id' => $doctor->id
                ])->id;
            },
            'caseable_type' => DoctorAccident::class,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/doctor/accidents/' . $accident->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson([
            'doctor_id' => $doctor->id
        ]);
    }

    public function testUpdate()
    {
        $user = factory(User::class)->create();
        $doctor = factory(Doctor::class)->create(['user_id' => $user->id]);
        $doctorAccident = factory(DoctorAccident::class)->create(['doctor_id' => $doctor->id]);

        $accident = factory(Accident::class)->create([
            'caseable_id' => function () use ($doctorAccident) {
                return $doctorAccident->id;
            },
            'caseable_type' => DoctorAccident::class,
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/doctor/accidents/' . $accident->id, ['diagnose' => 'Correct diagnose', 'status' => DoctorAccident::STATUS_SENT], ['Accept' => 'application/json']);

        $response->assertStatus(200);

        $updatedAccident = DoctorAccident::find($accident->id);

        self::assertEquals('Correct diagnose', $updatedAccident->diagnose);
        self::assertEquals(DoctorAccident::STATUS_SENT, $updatedAccident->status);
    }

    public function testForbiddenUpdate()
    {
        $user = factory(User::class)->create();
        $doctor = factory(Doctor::class)->create(['user_id' => $user->id]);
        $doctorAccident = factory(DoctorAccident::class)->create(['doctor_id' => $doctor->id]);

        $accident = factory(Accident::class)->create([
            'caseable_id' => function () use ($doctorAccident) {
                return $doctorAccident->id;
            },
            'caseable_type' => DoctorAccident::class,
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/doctor/accidents/' . $accident->id, ['status' => DoctorAccident::STATUS_PAID]
                    , ['Accept' => 'application/json']);

        $response->assertStatus(422);

        $response = $this
            ->actingAs($user)
            ->patch('/doctor/accidents/' . $accident->id, ['status' => DoctorAccident::STATUS_CLOSED]
                , ['Accept' => 'application/json']);

        $response->assertStatus(422);


        $response = $this
            ->actingAs($user)
            ->patch('/doctor/accidents/' . $accident->id, ['status' => DoctorAccident::STATUS_NEW]
                , ['Accept' => 'application/json']);

        $response->assertStatus(422);


        $response = $this
            ->actingAs($user)
            ->patch('/doctor/accidents/' . $accident->id, ['status' => DoctorAccident::STATUS_SIGNED]
                , ['Accept' => 'application/json']);

        $response->assertStatus(200);


        $response = $this
            ->actingAs($user)
            ->patch('/doctor/accidents/' . $accident->id, ['status' => DoctorAccident::STATUS_IN_PROGRESS]
                , ['Accept' => 'application/json']);

        $response->assertStatus(200);


        $response = $this
            ->actingAs($user)
            ->patch('/doctor/accidents/' . $accident->id, ['city_id' => 1]
                , ['Accept' => 'application/json']);

        $response->assertStatus(422);

        $response = $this
            ->actingAs($user)
            ->patch('/doctor/accidents/' . $accident->id, ['doctor_id' => 1]
                , ['Accept' => 'application/json']);

        $response->assertStatus(422);
    }

}
