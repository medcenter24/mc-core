<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\AccidentStatus;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StatusesTest extends TestCase
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
            ->get('/director/statuses');

        $response->assertStatus(200)
                ->assertJson([]);
    }


    public function testEmptyStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/statuses', [], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(422)->assertJson([
            'title' => ['The title field is required.'],
            'description' => ['The description field is required.'],
            'caseable_type' => ['The caseable type field is required.'],
        ]);

        //
        self::assertEquals('{"title":["The title field is required."],"description":["The description field is required."],"caseable_type":["The caseable type field is required."]}', $response->getContent());
    }

    public function testStore()
    {
        $data = [
            'title' => 'My first Stored Accident status',
            'description' => 'Anything about stored accident status',
            'caseable_type' => 'Bind to any of Accidents'
        ];

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/statuses', $data, [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson($data);
    }

    public function testShow()
    {
        $status = factory(AccidentStatus::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/statuses/' . $status->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($status->toArray());
    }

    public function testUpdate()
    {
        $status = factory(AccidentStatus::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/statuses/1', [
                'title' => 'Replaced by this'
            ]);

        $response->assertStatus(200);

        $source = $status->toArray();
        $source['title'] = 'Replaced by this';

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/statuses/' . $status->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $status = factory(AccidentStatus::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/statuses/' . $status->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($status->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/statuses/' . $status->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/statuses/' . $status->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = AccidentStatus::withTrashed()->find($status->id);
        self::assertEquals($status->id, $deleted->id, 'Soft deleted');
    }
}
