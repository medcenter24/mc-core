<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Director;

use App\Accident;
use App\AccidentStatus;
use App\AccidentType;
use App\Assistant;
use App\DoctorAccident;
use App\Patient;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AccidentsTest extends TestCase
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
            ->get('/director/accidents');

        $response->assertStatus(200)->assertJson([]);
    }


    public function testEmptyStore()
    {
        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/accidents', [], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(422)->assertJson([]);

        //
        self::assertEquals(
            '{"created_by":["The created by field is required."],"patient_id":["The patient id field is required."],"accident_type_id":["The accident type id field is required."],"accident_status_id":["The accident status id field is required."],"caseable_id":["The caseable id field is required."],"caseable_type":["The caseable type field is required."],"ref_num":["The ref num field is required."],"title":["The title field is required."]}'
            , $response->getContent());
    }

    public function testStore()
    {
        $accident = factory(Accident::class)->make();
        $ret = $accident->toArray();
        $data = $accident->makeVisible(['created_by', 'patient_id', 'caseable_id', 'caseable_type'])->toArray();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->post('/director/accidents', $data, [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)->assertJson($ret);
    }

    public function testShow()
    {
        $assistant = factory(Accident::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/accidents/' . $assistant->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($assistant->toArray());
    }

    public function testUpdate()
    {
        $assistant = factory(Accident::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->patch('/director/accidents/' . $assistant->id, [
                'title' => 'Replaced by this'
            ]);

        $response->assertStatus(200);

        $source = $assistant->toArray();
        $source['title'] = 'Replaced by this';

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/accidents/' . $assistant->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($source);
    }

    public function testDelete()
    {
        $assistant = factory(Accident::class)->create();

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/accidents/' . $assistant->id, ['Accept' => 'application/json']);

        $response->assertStatus(200)->assertJson($assistant->toArray());

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->delete('/director/accidents/' . $assistant->id);

        $response->assertStatus(200);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/director/accidents/' . $assistant->id, ['Accept' => 'application/json']);

        $response->assertStatus(404);

        $deleted = Accident::withTrashed()->find($assistant->id);
        self::assertEquals($assistant->id, $deleted->id, 'Soft deleted');
    }
}
