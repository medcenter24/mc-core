<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director\Cases\CaseController;

use App\Accident;
use App\FinanceCurrency;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;

class CaseControllerFinanceActionTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testGetFinance404(): void {
        $response = $this->json('POST', '/api/director/cases/1/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(404);
        $response->assertJson([]);
    }

    public function testGetFinance(): void {
        $accident = factory(Accident::class)->create();
        factory(FinanceCurrency::class)->create();
        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $data = $response->getContent();
        $response->assertStatus(404);
        $response->assertJson([]);
    }
}
