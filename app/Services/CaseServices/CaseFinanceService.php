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
use App\DoctorAccident;
use App\DoctorService;
use App\Exceptions\InconsistentDataException;
use App\FinanceCondition;
use App\FinanceStorage;
use App\HospitalAccident;
use App\Http\Requests\Api\FinanceRequest;
use App\Models\Cases\Finance\CaseFinanceCondition;
use App\Services\AccidentService;
use App\Services\FinanceConditionService;
use App\Services\Formula\FormulaService;

class CaseFinanceService
{
    /**
     * @var FormulaService
     */
    private $formulaService;

    /**
     * @var AccidentService
     */
    private $accidentService;

    /**
     * CaseFinanceService constructor.
     * @param FormulaService $formulaService
     * @param AccidentService $accidentService
     */
    public function __construct(FormulaService $formulaService, AccidentService $accidentService)
    {
        $this->formulaService = $formulaService;
        $this->accidentService = $accidentService;
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
     * Assistant's income minus doctor's payment or the hospitals' payment
     * @param Accident $accident
     * @return float|int
     */
    public function calculateIncome(Accident $accident)
    {
        $caseablePayment = 0;
        $income = $this->calculateFromAssistantPayment($accident);
        if ($accident->caseable_type == DoctorAccident::class) {
            $caseablePayment = $this->calculateToDoctorPayment($accident);
        } elseif ($accident->caseable_type == HospitalAccident::class) {
            $caseablePayment = $this->calculateToHospitalPayment($accident);
        }

        $income = $income - $caseablePayment;
        if ($income <= 0) {
            \Log::emergency('Wasting money on the accident', [
                'accident_id' => $accident->id,
                'income' => $income,
                'caseablePayment' => $caseablePayment,
            ]);
        }

        return $income;
    }

    /**
     * Payment from the company to the doctor
     * @param Accident $accident
     * @return int
     */
    public function calculateToDoctorPayment(Accident $accident)
    {
        $doctorServices = $this->accidentService->getAccidentServices($accident);
        $conditionProps = [
            DatePeriod::class => $accident->handling_time,
            Doctor::class => $accident->caseable_id,
            Assistant::class => $accident->assistant_id,
            City::class => $accident->city_id,
            DoctorService::class => $doctorServices ? $doctorServices->get('id') : false,
        ];
        $conditions = FinanceConditionService::findConditions($conditionProps);

        // calculate formula by conditions
        $formula = $this->formulaService->formula();
        var_dump($conditions);
        return 1;
    }

    public function calculateToHospitalPayment(Accident $accident)
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
     * // todo move it from this service, I can't use requests here
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
        $caseFinanceCondition->setConditionType($request->json('type', FinanceConditionService::PARAM_TYPE_ADD));
        $caseFinanceCondition->setCurrencyMode($request->json('currencyMode', FinanceConditionService::PARAM_CURRENCY_MODE_PERCENT));
        $caseFinanceCondition->setCurrency($request->json('currencyId', 0));
        $caseFinanceCondition->setModel($request->json('model', Accident::class));

        return $this->saveCondition($caseFinanceCondition, $id);
    }
}
