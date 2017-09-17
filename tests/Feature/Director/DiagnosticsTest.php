<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\Diagnostic;
use App\DiagnosticCategory;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class diagnosticsTest extends TestCase
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
            ->get('/director/diagnostics');

        $response->assertStatus(200)->assertJson([]);
    }


    public function testEmptyStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/diagnostics', [], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(422)->assertJson([
            'title' => ['The title field is required.'],
            'description' => ['The description field is required.'],
            "diagnostic_category_id" => ["The diagnostic category id field is required."]
        ]);

        //
        self::assertEquals('{"title":["The title field is required."],"description":["The description field is required."],"diagnostic_category_id":["The diagnostic category id field is required."]}', $response->getContent());
    }

    public function testStore()
    {
        $data = [
            'title' => 'Has already done',
            'description' => 'What about that title?',
            'diagnostic_category_id' => factory(DiagnosticCategory::class)->create()->id
        ];

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/diagnostics', $data, [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson($data);
    }

    public function testShow()
    {
        $diagnostic = factory(Diagnostic::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/diagnostics/' . $diagnostic->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($diagnostic->toArray());
    }

    public function testUpdate()
    {
        $diagnostic = factory(Diagnostic::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/diagnostics/' . $diagnostic->id, [
                'title' => 'Replaced by this'
            ]);

        $response->assertStatus(200);

        $source = $diagnostic->toArray();
        $source['title'] = 'Replaced by this';

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/diagnostics/' . $diagnostic->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $diagnostic = factory(Diagnostic::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/diagnostics/' . $diagnostic->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($diagnostic->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/diagnostics/' . $diagnostic->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/diagnostics/' . $diagnostic->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = Diagnostic::withTrashed()->find($diagnostic->id);
        self::assertEquals($diagnostic->id, $deleted->id, 'Soft deleted');
    }
}
