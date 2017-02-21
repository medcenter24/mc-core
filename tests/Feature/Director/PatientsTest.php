<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\Patient;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PatientsTest extends TestCase
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
            ->get('/director/patients');

        $response->assertStatus(200)->assertJson([]);
    }


    public function testEmptyStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/patients', [], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(422)->assertJson([]);

        //
        self::assertEquals('{"name":["The name field is required."]}', $response->getContent());
    }

    public function testStore()
    {
        $data = [
            'name' => 'Abigail Forester',
        ];

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/patients', $data, [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson($data);
    }

    public function testStoreFull()
    {
        $data = [
            'name' => 'Abigail Forester',
            'address' => 'Gikalo St. 22',
            'phones' => '+375053672757,+380953055704',
            'birthday' => '17.05.1987',
            'comment' => 'Look who is this',
        ];

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/patients', $data, [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson($data);
    }

    public function testShow()
    {
        $patient = factory(Patient::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/patients/' . $patient->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($patient->toArray());
    }

    public function testUpdate()
    {
        $patient = factory(Patient::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/patients/' . $patient->id, [
                'name' => 'Replaced by this'
            ]);

        $response->assertStatus(200);

        $source = $patient->toArray();
        $source['name'] = 'Replaced by this';
        // json doesn't return empty fields
        unset($source['deleted_at']);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/patients/' . $patient->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $patient = factory(Patient::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/patients/' . $patient->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($patient->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/patients/' . $patient->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/patients/' . $patient->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = Patient::withTrashed()->find($patient->id);
        self::assertEquals($patient->id, $deleted->id, 'Soft deleted');
    }
}
