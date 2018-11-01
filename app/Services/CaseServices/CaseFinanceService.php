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
use App\FinanceCurrency;
use App\FinanceStorage;
use App\Hospital;
use App\HospitalAccident;
use App\Http\Requests\Api\FinanceRequest;
use App\Models\Cases\Finance\CaseFinanceCondition;
use App\Models\Formula\FormulaBuilder;
use App\Models\Formula\FormulaBuilderInterface;
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
     * @var FinanceConditionService
     */
    private $financeConditionService;

    /**
     * CaseFinanceService constructor.
     * @param FormulaService $formulaService
     * @param AccidentService $accidentService
     * @param FinanceConditionService $financeConditionService
     */
    public function __construct(
        FormulaService $formulaService,
        AccidentService $accidentService,
        FinanceConditionService $financeConditionService
    ) {
        $this->formulaService = $formulaService;
        $this->accidentService = $accidentService;
        $this->financeConditionService = $financeConditionService;
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
     * @return int
     * @throws InconsistentDataException
     */
    public function calculateIncome(Accident $accident)
    {
        $income = $this->calculateFromAssistantPayment($accident);
        switch ($accident->caseable_type) {
            case DoctorAccident::class :
                $caseablePayment = $this->calculateToDoctorPayment($accident);
                break;
            case HospitalAccident::class :
                $caseablePayment = $this->calculateToHospitalPayment($accident);;
                break;
            default:
                $caseablePayment = 0;
        }

        $income = $income - $caseablePayment;
        if ($income <= 0 && \App::environment() == 'production') {
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
     * @throws InconsistentDataException
     */
    public function calculateToDoctorPayment(Accident $accident)
    {
        if ($accident->caseable_type != DoctorAccident::class) {
            throw new InconsistentDataException('DoctorAccident only');
        }

        $amount = 0;

        $doctorServices = $this->accidentService->getAccidentServices($accident);
        $conditionProps = [
            DatePeriod::class => $accident->handling_time,
            DoctorAccident::class => $accident->caseable_id,
            Assistant::class => $accident->assistant_id,
            City::class => $accident->city_id,
            DoctorService::class => $doctorServices ? $doctorServices->get('id') : false,
        ];
        $conditions = $this->financeConditionService->findConditions($conditionProps);

        // calculate formula by conditions
        if ($conditions->count()) {
            $formula = $this->formulaService->formula();
            die('dp has not implemented yet');
        }
        return $amount;
    }

    /**
     * @param Accident $accident
     * @return int
     * @throws InconsistentDataException
     */
    public function calculateToHospitalPayment(Accident $accident)
    {
        if ($accident->caseable_type != HospitalAccident::class) {
            throw new InconsistentDataException('Hospital Case only');
        }

        $amount = 0;

        $conditionProps = [
            DatePeriod::class => $accident->handling_time,
            HospitalAccident::class => $accident->caseable_id,
            Assistant::class => $accident->assistant_id,
            City::class => $accident->city_id,
        ];
        $conditions = $this->financeConditionService->findConditions($conditionProps);

        // calculate formula by conditions
        if ($conditions->count()) {
            $formula = $this->formulaService->formula();
            var_dump($formula);
            $amount = $formula->getResult();
        }

        return $amount;
    }

    /**
     * Payment from the assistant to the company
     * Price from the invoice
     * @param Accident $accident
     * @return int
     */
    public function calculateFromAssistantPayment(Accident $accident)
    {
        $result = 0;
        // check that the value was not stored yet
        if ($payment = $accident->getAttribute('paymentFromAssistant')) {
            // if stored then show this value
            $result = $payment->getAttribute('value');
        } else {
            // if doesn't stored - calculate
            $formula = $this->formulaService->formula();
            // 1. take amout from the invoice from assistant
            $guaranteePrice = $accident->assistantGuarantee ? $accident->assistantGuarantee->price : 0;
            $formula->addFloat($guaranteePrice);
            // 2. apply formula, which bind to this Assistant and has type Assistant
            $conditionProps = [
                DatePeriod::class => $accident->handling_time,
                HospitalAccident::class => $accident->caseable_id,
                Assistant::class => $accident->assistant_id,
                City::class => $accident->city_id,
            ];
            $conditions = $this->financeConditionService->findConditions($conditionProps);
            if ($conditions->count()) {
                $conditions->each(function(FinanceCondition $condition) use ($formula) {
                    $this->appendConditionToFormula($condition, $formula);
                });
                $result = $formula->getResult();
            }

        }
        return $result;
    }

    /**
     * @param FinanceCondition $condition
     * @param FormulaBuilderInterface $formula
     * @throws InconsistentDataException
     */
    public function appendConditionToFormula(FinanceCondition $condition, FormulaBuilderInterface $formula)
    {
        $mode = $condition->getAttribute('currency_mode');
        switch ($mode) {
            case 'percent':
                if ($condition->getAttribute('type') == 'add') {
                    $formula->addPercent($condition->getAttribute('value'));
                } else {
                    $formula->subPercent($condition->getAttribute('value'));
                }
                break;
            case 'currency':
                $currency = FinanceCurrency::firstOrFail($condition->getAttribute('currency_id'));
                $val = $this->currencyService
                    ->convertCurrency(
                        $condition->getAttribute('value'),
                        $currency
                    );
                if ($condition->getAttribute('type') == 'add') {
                    $formula->addFloat($val);
                } else {
                    $formula->subFloat($val);
                }
                break;
            default:
                throw new InconsistentDataException('Undefined currency mode ' . $mode);
        }
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
