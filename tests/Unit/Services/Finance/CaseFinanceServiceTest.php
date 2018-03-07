<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Finance;


use App\Accident;
use App\Services\CaseServices\CaseFinanceService;
use Tests\TestCase;

class CaseFinanceServiceTest extends TestCase
{
    /**
     * @var CaseFinanceService
     */
    private $financeService;

    public function setUp()
    {
        parent::setUp();
        $this->financeService = new CaseFinanceService();
    }

    /**
     * Case without anything should return 0 for all valuable variables
     */
    public function testEmptyCase()
    {
        $accident = new Accident();
        self::assertEquals(0, $this->financeService->calculateIncome($accident), 'Income is correct');
        self::assertEquals(0, $this->financeService->calculateDoctorPayment($accident), 'Doctor payment is correct');
        self::assertEquals(0, $this->financeService->calculateAssistantPayment($accident), 'Assistant payment is correct');
    }
}
