<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices;


use App\Accident;
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
use App\Models\Formula\FormulaBuilderInterface;
use App\Services\FinanceService;
use App\Services\Formula\FormulaService;

class CaseFinanceService
{
    /**
     * @var FormulaService
     */
    private $formulaService;

    /**
     * CaseFinanceService constructor.
     * @param FormulaService $formulaService
     */
    public function __construct(FormulaService $formulaService)
    {
        $this->formulaService = $formulaService;
    }

    /**
     * New object
     * @return CaseFinanceCondition
     */
    public function createCondition()
    {
        return new CaseFinanceCondition();
    }

    /**
     * Creates or updates the conditions
     * @param CaseFinanceCondition $condition
     * @param int $id
     * @return mixed
     */
    public function saveCondition(CaseFinanceCondition $condition, $id = 0)
    {
        if ($id) {
            $financeCondition = FinanceCondition::findOrFail($id);
            $financeCondition->title = $condition->getTitle();
            $financeCondition->value = $condition->getValue();
            $financeCondition->currency_id = $condition->getCurrencyId();
            $financeCondition->currency_mode = $condition->getCurrencyMode();
            $financeCondition->type = $condition->getConditionType();
            $financeCondition->model = $condition->getModel();
            $financeCondition->save();
            $financeCondition->conditions()->delete(); // unassign all stored conditions
        } else {
            $financeCondition = FinanceCondition::create([
                'created_by' => auth()->id(),
                'title' => $condition->getTitle(),
                'value' => $condition->getValue(),
                'currency_id' => $condition->getCurrencyId(),
                'currency_mode' => $condition->getCurrencyMode(),
                'type' => $condition->getConditionType(),
                'model' => $condition->getModel(),
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

    /**
     * @param Accident $accident
     * @return FormulaBuilderInterface
     */
    protected function getFormula(Accident $accident)
    {
        return $this->formulaService->formula();
    }

    /**
     * Payment from the company to the doctor
     * @param Accident $accident
     * @return int
     */
    public function calculateToDoctorPayment(Accident $accident)
    {
        return 1;
    }

    /**
     * Payment from the assistant to the company
     * @param Accident $accident
     * @return int
     */
    public function calculateFromAssistantPayment(Accident $accident)
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
                $toCondition->if($className, is_array($model) ? $model['id'] : $model);
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
        $caseFinanceCondition = $this->createCondition();

        $this->addCondition($caseFinanceCondition, $request, Doctor::class, 'doctors');
        $this->addCondition($caseFinanceCondition, $request, Assistant::class, 'assistants');
        $this->addCondition($caseFinanceCondition, $request, City::class, 'cities');
        $this->addCondition($caseFinanceCondition, $request, DoctorService::class, 'services');
        $this->addCondition($caseFinanceCondition, $request, DatePeriod::class, 'datePeriods');

        $caseFinanceCondition->thenValue($request->json('value', 0));
        $caseFinanceCondition->setTitle($request->json('title', ''));
        $caseFinanceCondition->setConditionType($request->json('type', FinanceService::PARAM_TYPE_ADD));
        $caseFinanceCondition->setCurrencyMode($request->json('currencyMode', FinanceService::PARAM_CURRENCY_MODE_PERCENT));
        $caseFinanceCondition->setCurrency($request->json('currencyId', 0));
        $caseFinanceCondition->setModel($request->json('model', Accident::class));

        return $this->saveCondition($caseFinanceCondition, $id);
    }
}
