<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices;


use App\Accident;
use App\AccidentType;
use App\FinanceCondition;
use App\FinanceStorage;
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
        return new CaseFinanceCondition();
    }

    public function saveCondition(CaseFinanceCondition $condition, $title = '', $id = 0)
    {
        if ($id) {
            $financeCondition = FinanceCondition::findOrFail($id);
            $financeCondition->conditions()->delete(); // unassign all stored conditions
        } else {
            $financeCondition = FinanceCondition::create([
                'created_by' => auth()->id(),
                'title' => $title,
            ]);
        }
        $financeCondition->price = $condition->getPrice();
        $financeCondition->save();

        // store conditions
        $collection = $condition->getCondition()->getIterator();
        while ($collection->valid()) {
            $op = $collection->current();
            FinanceStorage::create([
                'finance_condition_id' => $financeCondition,
                'model' => $op->modelName(),
                'model_id' => $op->id(),
            ]);
            $collection->next();
        }
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
        return;
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
