<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\City;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CitiesTest extends TestCase
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
            ->get('/director/cities');

        $response->assertStatus(200)->assertJson([]);
    }


    public function testEmptyStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/cities', [], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(422)->assertJson([
            'title' => ['The title field is required.'],
        ]);

        //
        self::assertEquals('{"title":["The title field is required."]}', $response->getContent());
    }

    public function testStore()
    {
        $data = [
            'title' => 'Has already done',
        ];

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/cities', $data, [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson($data);
    }

    public function testShow()
    {
        $city = factory(City::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/cities/' . $city->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($city->toArray());
    }

    public function testUpdate()
    {
        $city = factory(City::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/cities/' . $city->id, [
                'title' => 'Replaced by this'
            ]);

        $response->assertStatus(200);

        $source = $city->toArray();
        $source['title'] = 'Replaced by this';

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/cities/' . $city->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $city = factory(City::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/cities/' . $city->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($city->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/cities/' . $city->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/cities/' . $city->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = City::withTrashed()->find($city->id);
        self::assertEquals($city->id, $deleted->id, 'Soft deleted');
    }
}
