<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices;


use App\Accident;
use App\AccidentType;
use App\Models\Cases\Finance\CaseFinanceCondition;
use App\Services\Formula\FormulaService;

class CaseFinanceService
{
    /**
     * @var FormulaService
     */
    private $formulaService;

    public function __construct(FormulaService $formulaService)
    {
        $this->formulaService = $formulaService;
    }

    public function factory()
    {
        return new CaseFinanceCondition($this->formulaService);
    }

    /**
     * @param Accident $accident
     * @return float|int
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function calculateIncome(Accident $accident)
    {
        $formula = $this->getFormula($accident);
        return $this->formulaService->getResult($formula);
    }

    protected function getFormula(Accident $accident)
    {
    }

    /**
     * @param Accident $accident
     * @return int
     */
    public function calculateDoctorPayment(Accident $accident)
    {
        return 1;
    }

    /**
     * @param Accident $accident
     * @return int
     */
    public function calculateAssistantPayment(Accident $accident)
    {
        return 1;
    }
}
