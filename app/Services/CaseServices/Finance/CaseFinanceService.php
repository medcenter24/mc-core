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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services\CaseServices\Finance;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\DatePeriod;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Events\Accident\Payment\AccidentPaymentChangedEvent;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Entity\FinanceCondition;
use medcenter24\mcCore\App\Entity\FinanceStorage;
use medcenter24\mcCore\App\Entity\Hospital;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Exceptions\NotImplementedException;
use medcenter24\mcCore\App\Http\Requests\Api\FinanceConditionRequest;
use medcenter24\mcCore\App\Models\Cases\Finance\CaseFinanceCondition;
use medcenter24\mcCore\App\Models\Formula\Exception\FormulaException;
use medcenter24\mcCore\App\Models\Formula\FormulaBuilder;
use medcenter24\mcCore\App\Entity\Payment;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\CurrencyService;
use medcenter24\mcCore\App\Services\Entity\FinanceConditionService;
use medcenter24\mcCore\App\Services\Formula\FormulaService;
use medcenter24\mcCore\App\Contract\Formula\FormulaBuilder as FormulaBuilderContract;
use medcenter24\mcCore\App\Transformers\FinanceConditionTransformer;

class CaseFinanceService
{
    use ServiceLocatorTrait;

    private function getFormulaService(): FormulaService
    {
        return $this->getServiceLocator()->get(FormulaService::class);
    }

    private function getAccidentService(): AccidentService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }

    private function getFinanceConditionService(): FinanceConditionService
    {
        return $this->getServiceLocator()->get(FinanceConditionService::class);
    }

    private function getCurrencyService(): CurrencyService
    {
        return $this->getServiceLocator()->get(CurrencyService::class);
    }

    /**
     * @return FormulaBuilderContract
     */
    public function newFormula(): FormulaBuilderContract
    {
        return $this->getFormulaService()->createFormula();
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
            $financeCondition->order = $condition->getOrder();
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
                'order' => $condition->getOrder(),
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
     * @throws FormulaException|NotImplementedException
     */
    private function generateFormula(string $model, $conditionProps): FormulaBuilderContract {
        /** @var FormulaBuilder $formula */
        $formula = $this->newFormula();
        // delete empty values
        $conditionProps = array_filter($conditionProps);
        $conditions = $this->getFinanceConditionService()->findConditions($model, $conditionProps);

        // calculate formula by conditions
        if ($conditions->count()) {
            $formula = $this->getFormulaService()->createFormulaFromConditions($conditions);
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
     * @throws FormulaException
     */
    public function getToDoctorFormula(Accident $accident): FormulaBuilderContract
    {
        if (!$accident->isDoctorCaseable()) {
            throw new InconsistentDataException('DoctorAccident only');
        }

        $doctorServices = $this->getAccidentService()->getAccidentServices($accident);
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
            $conditionProps[Service::class] = $doctorServices->pluck('id')->all();
        }
        return $this->generateFormula(Doctor::class, $conditionProps);
    }

    /**
     * Static price or price from the invoice
     * @param Accident $accident
     * @return FormulaBuilderContract
     * @throws InconsistentDataException
     * @throws FormulaException
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
     * @throws FormulaException|NotImplementedException
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
     * @param FinanceConditionRequest $request
     * @param int $id
     * @return mixed
     */
    public function updateFinanceConditionByRequest(FinanceConditionRequest $request, $id = 0)
    {
        $caseFinanceCondition = $this->createCondition();

        $this->addCondition($caseFinanceCondition, $request, Doctor::class, 'doctors');
        $this->addCondition($caseFinanceCondition, $request, Assistant::class, 'assistants');
        $this->addCondition($caseFinanceCondition, $request, City::class, 'cities');
        $this->addCondition($caseFinanceCondition, $request, Service::class, 'services');
        $this->addCondition($caseFinanceCondition, $request, DatePeriod::class, 'datePeriods');

        $caseFinanceCondition->thenValue($request->json('value', 0));
        $caseFinanceCondition->setTitle($request->json('title', ''));
        $caseFinanceCondition->setConditionType($request->json('type', FinanceConditionService::PARAM_TYPE_ADD));
        $caseFinanceCondition->setCurrencyMode((string)$request->json('currencyMode',
            FinanceConditionService::PARAM_CURRENCY_MODE_PERCENT));
        $caseFinanceCondition->setCurrency($request->json('currencyId', 0));
        $caseFinanceCondition->setOrder((int)$request->json('order', 0));

        $financeConditionTransformer = new FinanceConditionTransformer();
        $modelName = $financeConditionTransformer->inverseTransformConditionModel($request->json('model', ''));
        $caseFinanceCondition->setModel($modelName);

        return $this->saveCondition($caseFinanceCondition, $id);
    }

    /**
     * Adding clause to the condition
     * @param $toCondition
     * @param FinanceConditionRequest $request
     * @param $className
     * @param $jsonKey
     */
    private function addCondition(CaseFinanceCondition $toCondition, FinanceConditionRequest $request, $className, $jsonKey): void
    {
        $data = $request->json($jsonKey, []);
        if ($data && count($data)) {
            foreach ($data as $model) {
                $id = (int) (is_array($model) ? $model['id'] : $model);
                $toCondition->if($className, $id);
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
            case 'cash':
                $this->savePayment($accident, $data, 'cashPayment');
                break;
            default:
                throw new InconsistentDataException('Undefined finance type');
        }
    }

    /**
     * @param Accident $accident
     * @param array $data
     * @param string $relationName
     */
    private function savePayment(Accident $accident, array $data, string $relationName): void
    {
        /** @var Payment $payment */
        $payment = $accident->$relationName;
        if ($payment) {
            $oldPayment = clone $payment;
            $payment->value = $data['price'];
            $payment->fixed = (bool)((int)$data['fixed']);
            $payment->save();
        } else {
            $oldPayment = null;
            $payment = Payment::create([
                'created_by' => auth()->user()->id,
                'value' => $data['price'],
                'currency_id' => $this->getCurrencyService()->getDefaultCurrency()->getAttribute('id'),
                'fixed' => (bool)((int)$data['fixed']),
                'description' => 'Created from CaseFinanceService',
            ]);
            $accident->$relationName()->associate($payment->id);
            $accident->save();
        }

        event(new AccidentPaymentChangedEvent($accident, $payment, $oldPayment));
    }
}
