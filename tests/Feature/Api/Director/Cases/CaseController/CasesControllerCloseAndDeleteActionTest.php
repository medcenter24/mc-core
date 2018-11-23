<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director\Cases\CaseController;


use App\Accident;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CasesControllerCloseAndDeleteActionTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testDeleteCase(){
        factory(Accident::class)->create();
        $response = $this->delete('/api/director/cases/1', [], $this->headers($this->getUser()));

        $response->assertStatus(204);
    }

    public function testCloseCase(){
        factory(Accident::class)->create();
        $response = $this->put('/api/director/cases/1/close', [], $this->headers($this->getUser()));

        $response->assertStatus(204);
    }
}
