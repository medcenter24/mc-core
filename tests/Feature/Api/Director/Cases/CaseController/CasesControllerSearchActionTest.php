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

class CasesControllerSearchActionTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testSearch()
    {
        factory(Accident::class, 7)->create();
        $response = $this->post('/api/director/cases/search', [], $this->headers($this->getUser()));

        $response->assertStatus(200)->assertJson([
            'data' => [
                ['id' => 1]
            ],
            "meta" => [
                "pagination" => [
                    "total" => 7,
                    "count" => 7,
                    "per_page" => 3000,
                    "current_page" => 1,
                    "total_pages" => 1,
                    "links" => []
                ]
            ]
        ]);
    }
}
