<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\DiagnosticCategory;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DiagnosticCategoriesTest extends TestCase
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
            ->get('/director/diagnostic/categories');

        $response->assertStatus(200)->assertJson([]);
    }


    public function testEmptyStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/diagnostic/categories', [], [
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
            ->post('/director/diagnostic/categories', $data, [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson($data);
    }

    public function testShow()
    {
        $DiagnosticCategory = factory(DiagnosticCategory::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/diagnostic/categories/' . $DiagnosticCategory->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($DiagnosticCategory->toArray());
    }

    public function testUpdate()
    {
        $DiagnosticCategory = factory(DiagnosticCategory::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/diagnostic/categories/' . $DiagnosticCategory->id, [
                'title' => 'Replaced by this'
            ]);

        $response->assertStatus(200);

        $source = $DiagnosticCategory->toArray();
        $source['title'] = 'Replaced by this';

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/diagnostic/categories/' . $DiagnosticCategory->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $DiagnosticCategory = factory(DiagnosticCategory::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/diagnostic/categories/' . $DiagnosticCategory->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($DiagnosticCategory->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/diagnostic/categories/' . $DiagnosticCategory->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/diagnostic/categories/' . $DiagnosticCategory->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = DiagnosticCategory::withTrashed()->find($DiagnosticCategory->id);
        self::assertEquals($DiagnosticCategory->id, $deleted->id, 'Soft deleted');
    }
}
