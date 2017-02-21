<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\Form;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FormsTest extends TestCase
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
            ->get('/director/forms');

        $response->assertStatus(200)->assertJson([]);
    }


    public function testEmptyStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/forms', [], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(422)->assertJson([]);

        //
        self::assertEquals('{"title":["The title field is required."],"description":["The description field is required."],"template":["The template field is required."]}', $response->getContent());
    }

    public function testStore()
    {
        $data = [
            'title' => 'Title',
            'description' => 'What about that title?',
            'template' => 'aat',
            'variables' => ''
        ];

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/forms', $data, [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson($data);
    }

    public function testShow()
    {
        $form = factory(Form::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/forms/' . $form->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($form->toArray());
    }

    public function testUpdate()
    {
        $form = factory(Form::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/forms/' . $form->id, [
                'title' => 'Replaced by this'
            ]);

        $response->assertStatus(200);

        $source = $form->toArray();
        $source['title'] = 'Replaced by this';

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/forms/' . $form->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $form = factory(Form::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/forms/' . $form->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($form->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/forms/' . $form->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/forms/' . $form->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = Form::withTrashed()->find($form->id);
        self::assertEquals($form->id, $deleted->id, 'Soft deleted');
    }
}
