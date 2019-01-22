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

    public function testGetFinanceWithoutCondition(): void {
        $accident = factory(Accident::class)->create();
        factory(FinanceCurrency::class)->create();
        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'value' => 0,
                    'currency' => [],
                    'formula' => '0.00 - 0.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'value' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'value' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
            ],
        ]);
    }
}
