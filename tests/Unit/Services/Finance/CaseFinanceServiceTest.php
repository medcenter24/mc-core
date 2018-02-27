<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Finance;


use App\Services\Finance\CaseFinanceService;
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
        $this->financeService->calculateIncome($accident);
        $this->financeService->calculateDoctorFee($accident);
        $this->financeService->calculateAssistantFee($accident);
    }
}
