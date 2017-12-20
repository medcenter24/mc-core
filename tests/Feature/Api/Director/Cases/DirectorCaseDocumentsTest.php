<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;

use App\Accident;
use App\Document;
use App\User;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectorCaseDocumentsTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testGetNoDocuments()
    {
        $case = factory(Accident::class)->create();
        $response = $this->get('/api/director/cases/' . $case->id .'/documents', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson(['data' => []]);
    }

    public function testGetDocuments()
    {
        $case = factory(Accident::class)->create();
        $docs = factory(Document::class, 5)->create();
        $case->documents()->attach($docs);
        self::assertEquals(5, $case->documents()->count());

        $response = $this->get('/api/director/cases/' . $case->id .'/documents', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson(['data' => [[], [], [], [], []]]);
    }

    public function testGetWithoutUsersDocuments()
    {
        $case = factory(Accident::class)->create();
        $docs = factory(Document::class, 5)->create();
        $case->documents()->attach($docs);
        self::assertEquals(5, $case->documents()->count());
        $user = $this->getUser();
        $docs = factory(Document::class, 2)->create();
        $user->documents()->attach($docs);
        self::assertEquals(2, $user->documents()->count());

        $response = $this->get('/api/director/cases/' . $case->id .'/documents', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson(['data' => [[], [], [], [], []]]);
    }


}
