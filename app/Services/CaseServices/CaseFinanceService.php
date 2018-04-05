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
use App\Doctor;
use App\DoctorService;
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
                'finance_condition_id' => $financeCondition,
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

    public function updateFinanceConditionByRequest(FinanceRequest $request, $id = 0)
    {
        $caseFinanceCondition = $this->factory();
        $doctor = $request->json('doctor', false);
        if ($doctor && isset($doctor['id'])) {
            $caseFinanceCondition->if(Doctor::class, $doctor['id']);
        }
        $assistant = $request->json('assistant', false);
        if ($assistant && isset($assistant['id'])) {
            $caseFinanceCondition->if(Assistant::class, $assistant['id']);
        }
        $city = $request->json('city', false);
        if ($city && isset($city['id'])) {
            $caseFinanceCondition->if(City::class, $city['id']);
        }

        // condition base on the full match
        // if you need to have only one service in condition or condition for each of the provided service:
        // then you need to create new conditions from the gui one by one
        $services = $request->json('services', false);
        if ($services && count($services)) {
            foreach ($services as $service) {
                $caseFinanceCondition->if(DoctorService::class, $service['id']);
            }
        }

        $caseFinanceCondition->thenPrice($request->json('priceAmount', 0));
        return $this->saveCondition(
            $caseFinanceCondition,
            $request->json('title', ''),
            $id
        );
    }
}
