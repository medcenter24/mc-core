<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices;


use App\Accident;
use App\AccidentType;
use App\Assistant;
use App\City;
use App\DatePeriod;
use App\Doctor;
use App\DoctorService;
use App\Exceptions\InconsistentDataException;
use App\FinanceCondition;
use App\FinanceStorage;
use App\Http\Requests\Api\FinanceRequest;
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
            $financeCondition->price = $condition->getPrice();
            $financeCondition->title = $title;
            $financeCondition->save();
            $financeCondition->conditions()->delete(); // unassign all stored conditions
        } else {
            $financeCondition = FinanceCondition::create([
                'created_by' => auth()->id(),
                'title' => $title,
                'price' => $condition->getPrice(),
            ]);
        }

        // store conditions
        $collection = $condition->getCondition()->getIterator();
        while ($collection->valid()) {
            $op = $collection->current();
            FinanceStorage::create([
                'finance_condition_id' => $financeCondition->id,
                'model' => $op->modelName(),
                'model_id' => $op->id(),
            ]);
            $collection->next();
        }

        return $financeCondition;
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

    /**
     * Adding clause to the condition
     * @param $toCondition
     * @param FinanceRequest $request
     * @param $className
     * @param $jsonKey
     * @throws InconsistentDataException
     */
    private function addCondition(CaseFinanceCondition &$toCondition, FinanceRequest $request, $className, $jsonKey)
    {
        $data = $request->json($jsonKey, []);
        if ($data && count($data)) {
            foreach ($data as $model) {
                $toCondition->if($className, $model['id']);
            }
        }
    }

    /**
     * Update or create condition
     * @param FinanceRequest $request
     * @param int $id
     * @return mixed
     * @throws InconsistentDataException
     */
    public function updateFinanceConditionByRequest(FinanceRequest $request, $id = 0)
    {
        $caseFinanceCondition = $this->factory();

        $this->addCondition($caseFinanceCondition, $request, Doctor::class, 'doctors');
        $this->addCondition($caseFinanceCondition, $request, Assistant::class, 'assistants');
        $this->addCondition($caseFinanceCondition, $request, City::class, 'cities');
        $this->addCondition($caseFinanceCondition, $request, DoctorService::class, 'services');
        $this->addCondition($caseFinanceCondition, $request, DatePeriod::class, 'datePeriods');

        $caseFinanceCondition->thenPrice($request->json('priceAmount', 0));
        return $this->saveCondition(
            $caseFinanceCondition,
            $request->json('title', ''),
            $id
        );
    }
}
