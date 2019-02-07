<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices\Finance;


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
use App\Hospital;
use App\HospitalAccident;
use App\Http\Requests\Api\FinanceRequest;
use App\Models\Cases\Finance\CaseFinanceCondition;
use App\Models\Formula\FormulaBuilder;
use App\Services\AccidentService;
use App\Services\FinanceConditionService;
use App\Services\Formula\FormulaService;
use App\Contract\Formula\FormulaBuilder as FormulaBuilderContract;

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
     * @return FormulaBuilderContract
     */
    public function newFormula(): FormulaBuilderContract
    {
        return $this->formulaService->createFormula();
    }

    /**
     * New object
     * @return CaseFinanceCondition
     */
    public function createCondition(): CaseFinanceCondition
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
            /** @var FinanceCondition $financeCondition */
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
     * Payment from the company to the doctor
     * @param Accident $accident
     * @return mixed
     * @throws InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function getToDoctorFormula(Accident $accident): FormulaBuilderContract
    {
        if ($accident->getAttribute('caseable_type') !== DoctorAccident::class) {
            throw new InconsistentDataException('DoctorAccident only');
        }

        /** @var FormulaBuilder $formula */
        $formula = $this->newFormula();
        if ($accident->paymentToCaseable && $accident->paymentToCaseable->fixed) {
            $formula->addFloat($accident->paymentToCaseable->value);
        } else {
            $doctorServices = $this->accidentService->getAccidentServices($accident);
            $conditionProps = [
                DatePeriod::class => $accident->handling_time,
                DoctorAccident::class => $accident->caseable_id,
                Assistant::class => $accident->assistant_id,
                City::class => $accident->city_id,
                DoctorService::class => $doctorServices ? $doctorServices->get('id') : false,
            ];
            $conditions = $this->financeConditionService->findConditions(Doctor::class, $conditionProps);

            // calculate formula by conditions
            if ($conditions->count()) {
                $formula = $this->formulaService->createFormulaFromConditions($conditions);
            } else {
                $formula->addFloat(); // to have 0 instead of ''
            }
        }
        return $formula;
    }

    /**
     * Static price or price from the invoice
     * @param Accident $accident
     * @return FormulaBuilderContract
     * @throws InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function getToHospitalFormula(Accident $accident): FormulaBuilderContract
    {
        if ($accident->caseable_type !== HospitalAccident::class) {
            throw new InconsistentDataException('Hospital Case only');
        }

        $formula = $this->newFormula();
        if ($accident->paymentToCaseable && $accident->paymentToCaseable->fixed) {
            $formula->addFloat($accident->paymentToCaseable->value);
        } else {
            // maybe I need this to have a possibility to influence on the value?
            $conditionProps = [
                DatePeriod::class => $accident->handling_time,
                HospitalAccident::class => $accident->caseable_id,
                Assistant::class => $accident->assistant_id,
                City::class => $accident->city_id,
            ];
            $conditions = $this->financeConditionService->findConditions(Hospital::class, $conditionProps);

            // calculate formula by conditions
            if ($conditions->count()) {
                $formula = $this->formulaService->createFormulaFromConditions($conditions);
            }

            // Invoice amount
            if ($accident->assistantInvoice && $accident->assistantInvoice->payment) {
                $formula->addFloat($accident->assistantInvoice->payment->value);
            }
        }

        return $formula;
    }

    /**
     * Payment from the assistant to the company
     * Price from the invoice
     * @param Accident $accident
     * @return FormulaBuilder
     */
    public function getFromAssistantFormula(Accident $accident): FormulaBuilderContract
    {
        $formula = $this->newFormula();
        // check that the value was not stored yet
        if ($accident->paymentFromAssistant && $accident->paymentFromAssistant->fixed) {
            // if stored then show this value
            // to do add currencies convert FinanceService::convert
            $formula->addFloat($accident->paymentFromAssistant->value);
        } else {
            // if doesn't stored - calculate
            // 1. take amount from the invoice from assistant
            // to do add currencies convert FinanceService::convert
            $guaranteePrice = $accident->assistantGuarantee ? $accident->assistantGuarantee->payment->value : 0;
            $formula->addFloat($guaranteePrice);
            // 2. apply formula, which bind to this Assistant and has type Assistant
            $conditionProps = [
                HospitalAccident::class => $accident->caseable_id,
                Assistant::class => $accident->assistant_id,
                City::class => $accident->city_id,
            ];
            if ($accident->getAttribute('handling_time')) {
                $conditionProps[DatePeriod::class] = $accident->getAttribute('handling_time');
            }
            $conditions = $this->financeConditionService->findConditions(Assistant::class, $conditionProps);
            if ($conditions->count()) {
                $formula = $this->formulaService->createFormulaFromConditions($conditions);
            }

        }

        return $formula;
    }

    /**
     * @param Accident $accident
     * @return FormulaBuilderContract
     * @throws InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function getToCaseableFormula(Accident $accident): FormulaBuilderContract
    {
        return $accident->getAttribute('caseable_type') === DoctorAccident::class
            ? $this->getToDoctorFormula($accident)
            : $this->getToHospitalFormula($accident);
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

    /**
     * Adding clause to the condition
     * @param $toCondition
     * @param FinanceRequest $request
     * @param $className
     * @param $jsonKey
     */
    private function addCondition(CaseFinanceCondition $toCondition, FinanceRequest $request, $className, $jsonKey): void
    {
        $data = $request->json($jsonKey, []);
        if ($data && count($data)) {
            foreach ($data as $model) {
                $toCondition->if($className, is_array($model) ? $model['id'] : $model);
            }
        }
    }
}
