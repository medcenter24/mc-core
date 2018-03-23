<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Cases\Finance;


use App\FinanceCondition;
use App\Formula;
use App\Models\Cases\Finance\Operations\IfOperation;
use App\Models\Formula\FormulaBuilderInterface;
use App\Services\Formula\FormulaService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CaseFinanceCondition
{
    /**
     * @var Collection
     */
    private $condition;

    /**
     * @var FormulaService
     */
    private $formulaService;

    public function __construct(FormulaService $formulaService)
    {
        $this->condition = collect([]);
        $this->formulaService = $formulaService;
    }

    /**
     * @param Model|string $modelName
     * @param int $id
     * @return $this
     */
    public function if(string $modelName, int $id)
    {
        $op = new IfOperation($modelName, $id);
        $this->condition->push($op);
        return $this;
    }

    public function thenDoctorPaymentFormula(FormulaBuilderInterface $formulaBuilder)
    {
        return $this->store($this->condition, $formulaBuilder, 'doctorPayment');
    }

    public function thenAssistantPaymentFormula(FormulaBuilderInterface $formulaBuilder)
    {
        return $this->store($this->condition, $formulaBuilder, 'assistantPayment');
    }

    /**
     * @param Collection $conditionCollection
     * @param FormulaBuilderInterface $formulaBuilder
     * @param string $type
     * @return FinanceCondition
     */
    protected function store(Collection $conditionCollection, FormulaBuilderInterface $formulaBuilder, $type = '')
    {
        $formula = $this->saveFormula($formulaBuilder);
        return $this->saveCondition($conditionCollection, $formula, $type);
    }

    protected function saveFormula(FormulaBuilderInterface $formula)
    {
        return $this->formulaService->store($formula);
    }

    protected function saveCondition(Collection $conditionCollection, Formula $formula, $type = '')
    {
        $financeCondition = FinanceCondition::firstOrCreate([
            'formula_id' => $formula->id,
            'type' => $type,
        ]);
        return $financeCondition;
    }

    protected function assign(Formula $formula, FinanceCondition $financeCondition)
    {

    }
}
