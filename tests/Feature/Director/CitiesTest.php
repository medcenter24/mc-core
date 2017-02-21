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

        $response->assertStatus(200)->assertJson([
            'title' => $data['title'],
        ]);
    }

    public function testShow()
    {
        $checkpoint = factory(City::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/cities/' . $checkpoint->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($checkpoint->toArray());
    }

    public function testUpdate()
    {
        $checkpoint = factory(City::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/cities/' . $checkpoint->id, [
                'title' => 'Replaced by this'
            ]);

        $response->assertStatus(200);

        $source = $checkpoint->toArray();
        $source['title'] = 'Replaced by this';

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/cities/' . $checkpoint->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $checkpoint = factory(City::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/cities/' . $checkpoint->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($checkpoint->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/cities/' . $checkpoint->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/cities/' . $checkpoint->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = City::withTrashed()->find($checkpoint->id);
        self::assertEquals($checkpoint->id, $deleted->id, 'Soft deleted');
    }
}
