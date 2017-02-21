<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\DoctorService;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ServicesTest extends TestCase
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
            ->get('/director/services');

        $response->assertStatus(200)->assertJson([]);
    }


    public function testEmptyStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/services', [], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(422)->assertJson([]);

        //
        self::assertEquals('{"created_by":["The created by field is required."],"title":["The title field is required."],"description":["The description field is required."],"price":["The price field is required."]}', $response->getContent());
    }

    public function testStore()
    {
        $data = [
            'created_by' => 0,
            'title' => 'Anything in the title',
            'description' => 'What about that title?',
            'price' => 80,
        ];

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/services', $data, [
                'Accept' => 'application/json'
            ]);

        // created_by is not visible,so shouldn't be returned
        unset($data['created_by']);
        $response->assertStatus(200)->assertJson($data);
    }

    public function testShow()
    {
        $service = factory(DoctorService::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/services/' . $service->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($service->toArray());
    }

    public function testUpdate()
    {
        $service = factory(DoctorService::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/services/' . $service->id, [
                'title' => 'Replaced by this'
            ]);

        $response->assertStatus(200);

        $source = $service->toArray();
        $source['title'] = 'Replaced by this';

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/services/' . $service->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $service = factory(DoctorService::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/services/' . $service->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($service->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/services/' . $service->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/services/' . $service->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = DoctorService::withTrashed()->find($service->id);
        self::assertEquals($service->id, $deleted->id, 'Soft deleted');
    }
}
