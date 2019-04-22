<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Services\CaseServices\Finance;


use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Assistant;
use medcenter24\mcCore\App\City;
use medcenter24\mcCore\App\DatePeriod;
use medcenter24\mcCore\App\Doctor;
use medcenter24\mcCore\App\DoctorAccident;
use medcenter24\mcCore\App\DoctorService;
use medcenter24\mcCore\App\Events\AccidentPaymentChangedEvent;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\FinanceCondition;
use medcenter24\mcCore\App\FinanceStorage;
use medcenter24\mcCore\App\Hospital;
use medcenter24\mcCore\App\HospitalAccident;
use medcenter24\mcCore\App\Http\Requests\Api\FinanceRequest;
use medcenter24\mcCore\App\Models\Cases\Finance\CaseFinanceCondition;
use medcenter24\mcCore\App\Models\Formula\FormulaBuilder;
use medcenter24\mcCore\App\Payment;
use medcenter24\mcCore\App\Services\AccidentService;
use medcenter24\mcCore\App\Services\CurrencyService;
use medcenter24\mcCore\App\Services\FinanceConditionService;
use medcenter24\mcCore\App\Services\Formula\FormulaService;
use medcenter24\mcCore\App\Contract\Formula\FormulaBuilder as FormulaBuilderContract;
use DemeterChain\C;

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
     * @var CurrencyService
     */
    private $currencyService;

    /**
     * CaseFinanceService constructor.
     * @param FormulaService $formulaService
     * @param AccidentService $accidentService
     * @param FinanceConditionService $financeConditionService
     * @param CurrencyService $currencyService
     */
    public function __construct(
        FormulaService $formulaService,
        AccidentService $accidentService,
        FinanceConditionService $financeConditionService,
        CurrencyService $currencyService
    ) {
        $this->formulaService = $formulaService;
        $this->accidentService = $accidentService;
        $this->financeConditionService = $financeConditionService;
        $this->currencyService = $currencyService;
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
     * @param string $model
     * @param $conditionProps
     * @return FormulaBuilderContract
     * @throws \medcenter24/mcCore\Models\Formula\Exception\FormulaException
     */
    private function generateFormula(string $model, $conditionProps): FormulaBuilderContract {
        /** @var FormulaBuilder $formula */
        $formula = $this->newFormula();
        // delete empty values
        $conditionProps = array_filter($conditionProps);
        $conditions = $conditionProps ? $this->financeConditionService->findConditions($model, $conditionProps) : false;

        // calculate formula by conditions
        if ($conditions && $conditions->count()) {
            $formula = $this->formulaService->createFormulaFromConditions($conditions);
        } else {
            $formula->addFloat(); // to have 0 instead of ''
        }

        return $formula;
    }

    /**
     * Payment from the company to the doctor
     * @param Accident $accident
     * @return mixed
     * @throws InconsistentDataException
     * @throws \medcenter24/mcCore\Models\Formula\Exception\FormulaException
     */
    public function getToDoctorFormula(Accident $accident): FormulaBuilderContract
    {
        if (!$accident->isDoctorCaseable()) {
            throw new InconsistentDataException('DoctorAccident only');
        }

        $doctorServices = $this->accidentService->getAccidentServices($accident);
        $conditionProps = [
            DatePeriod::class => $accident->handling_time,
            DoctorAccident::class => $accident->caseable_id,
            Assistant::class => $accident->assistant_id,
            City::class => $accident->city_id,
        ];

        if ($accident->caseable) {
            $conditionProps[Doctor::class] = $accident->caseable->doctor_id;
        }

        if ($doctorServices->count()) {
            $conditionProps[DoctorService::class] = $doctorServices->pluck('id')->all();
        }
        return $this->generateFormula(Doctor::class, $conditionProps);
    }

    /**
     * Static price or price from the invoice
     * @param Accident $accident
     * @return FormulaBuilderContract
     * @throws InconsistentDataException
     * @throws \medcenter24/mcCore\Models\Formula\Exception\FormulaException
     */
    public function getToHospitalFormula(Accident $accident): FormulaBuilderContract
    {
        if (!$accident->isHospitalCaseable()) {
            throw new InconsistentDataException('Hospital Case only');
        }

        // maybe I need this to have a possibility to influence on the value?
        $conditionProps = [
            DatePeriod::class => $accident->handling_time,
            HospitalAccident::class => $accident->caseable_id,
            Assistant::class => $accident->assistant_id,
            City::class => $accident->city_id,
        ];
        return $this->generateFormula(Hospital::class, $conditionProps);
    }

    /**
     * Payment from the assistant to the company
     * @param Accident $accident
     * @return FormulaBuilderContract
     * @throws \medcenter24/mcCore\Models\Formula\Exception\FormulaException
     */
    public function getFromAssistantFormula(Accident $accident): FormulaBuilderContract
    {
        // 1. take amount from the invoice from assistant
        // todo add currencies convert FinanceService::convert
        // 2. apply formula, which bind to this Assistant and has type Assistant
        $conditionProps = [
            HospitalAccident::class => $accident->caseable_id,
            Assistant::class => $accident->assistant_id,
            City::class => $accident->city_id,
            DatePeriod::class => $accident->getAttribute('handling_time'),
        ];
        return $this->generateFormula(Assistant::class, $conditionProps);
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

    /**
     * @param Accident $accident
     * @param string $type
     * @param array $data
     * @throws InconsistentDataException
     */
    public function save(Accident $accident, string $type, array $data): void
    {
        switch ($type) {
            case 'income':
                $this->savePayment($accident, $data, 'incomePayment');
                break;
            case 'assistant':
                $this->savePayment($accident, $data, 'paymentFromAssistant');
                break;
            case 'caseable':
                $this->savePayment($accident, $data, 'paymentToCaseable');
                break;
            default:
                throw new InconsistentDataException('Undefined finance type');
        }
    }

    /**
     * @param Accident $accident
     * @param array $data
     * @param $relationName
     */
    private function savePayment(Accident $accident, array $data, $relationName): void
    {
        /** @var Payment $payment */
        $payment = $accident->$relationName;
        if ($payment) {
            $oldPayment = clone $payment;
            $payment->value = $data['price'];
            $payment->fixed = (int) $data['fixed'] ? true : false;
            $payment->save();
        } else {
            $oldPayment = null;
            $payment = Payment::create([
                'created_by' => auth()->user()->id,
                'value' => $data['price'],
                'currency_id' => $this->currencyService->getDefaultCurrency()->getAttribute('id'),
                'fixed' => (int)$data['fixed'] ? true : false,
                'description' => 'Created from CaseFinanceService',
            ]);
            $accident->$relationName()->associate($payment->id);
            $accident->save();
        }

        event(new AccidentPaymentChangedEvent($accident, $payment, $oldPayment));
    }
}
