<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\Assistant;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AssistantsTest extends TestCase
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
            ->get('/director/assistants');

        $response->assertStatus(200)->assertJson([]);
    }


    public function testEmptyStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/assistants', [], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(422)->assertJson([]);

        //
        self::assertEquals('{"title":["The title field is required."],"ref_key":["The ref key field is required."]}', $response->getContent());
    }

    public function testStore()
    {
        $data = [
            'title' => 'Title',
            'comment' => 'What about that title?',
            'ref_key' => 'aat',
        ];

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/assistants', $data, [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson($data);
    }

    public function testShow()
    {
        $assistant = factory(Assistant::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/assistants/' . $assistant->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($assistant->toArray());
    }

    public function testUpdate()
    {
        $assistant = factory(Assistant::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/assistants/' . $assistant->id, [
                'title' => 'Replaced by this'
            ]);

        $response->assertStatus(200);

        $source = $assistant->toArray();
        $source['title'] = 'Replaced by this';

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/assistants/' . $assistant->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $assistant = factory(Assistant::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/assistants/' . $assistant->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($assistant->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/assistants/' . $assistant->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/assistants/' . $assistant->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = Assistant::withTrashed()->find($assistant->id);
        self::assertEquals($assistant->id, $deleted->id, 'Soft deleted');
    }
}
