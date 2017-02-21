<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\Doctor;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DoctorsTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;

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
            ->get('/director/doctors');

        $response->assertStatus(200)->assertJson([]);
    }


    public function testEmptyStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/doctors', [], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(422)->assertJson([]);

        //
        self::assertEquals('{"name":["The name field is required."],"description":["The description field is required."],"ref_key":["The ref key field is required."]}', $response->getContent());
    }

    public function testStore()
    {
        $data = [
            'name' => 'Dr. Smith',
            'description' => 'What about that title?',
            'ref_key' => 'DSW',
        ];

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/doctors', $data, [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson([
            'name' => $data['name'],
            'description' => $data['description'],
            'ref_key' => $data['ref_key'],
        ]);
    }

    public function testShow()
    {
        $doctor = factory(Doctor::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/doctors/' . $doctor->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($doctor->toArray());
    }

    public function testUpdate()
    {
        $doctor = factory(Doctor::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/doctors/' . $doctor->id, [
                'name' => 'Replaced by this'
            ]);

        $response->assertStatus(200);

        $source = $doctor->toArray();
        $source['name'] = 'Replaced by this';

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/doctors/' . $doctor->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $doctor = factory(Doctor::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/doctors/' . $doctor->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($doctor->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/doctors/' . $doctor->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/doctors/' . $doctor->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = Doctor::withTrashed()->find($doctor->id);
        self::assertEquals($doctor->id, $deleted->id, 'Soft deleted');
    }
}
