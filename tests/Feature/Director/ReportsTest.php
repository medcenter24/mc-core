<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\FormReport;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReportsTest extends TestCase
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
            ->get('/director/reports');

        $response->assertStatus(200)->assertJson([]);
    }


    public function testEmptyStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/reports', [], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson([]);
    }

    public function testStore()
    {
        $data = [
            'values' => json_encode(['key_1' => 'val_1', 'key_2' => 'val_2'])
        ];

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/reports', $data, [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson($data);
    }

    public function testShow()
    {
        $form = factory(FormReport::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/reports/' . $form->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($form->toArray());
    }

    public function testUpdate()
    {
        $form = factory(FormReport::class)->create();

        $test = ['values' => json_encode(['k_1' => 'v_1'])];

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/reports/' . $form->id, $test);

        $response->assertStatus(200);

        $source = $form->toArray();
        $source = array_merge($source, $test);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/reports/' . $form->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $form = factory(FormReport::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/reports/' . $form->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($form->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/reports/' . $form->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/reports/' . $form->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = FormReport::withTrashed()->find($form->id);
        self::assertEquals($form->id, $deleted->id, 'Soft deleted');
    }
}
