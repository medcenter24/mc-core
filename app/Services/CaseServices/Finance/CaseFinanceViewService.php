<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices\Finance;


use App\Accident;
use App\Exceptions\InconsistentDataException;
use App\Payment;
use App\Services\CurrencyService;
use App\Services\Formula\FormulaResultService;
use App\Services\Formula\FormulaViewService;
use Illuminate\Support\Collection;

class CaseFinanceViewService
{
    public const FINANCE_TYPES = ['income', 'assistant', 'caseable'];

    /**
     * @var CaseFinanceService
     */
    private $caseFinanceService;

    /**
     * @var FormulaResultService
     */
    private $formulaResultService;

    /**
     * @var CurrencyService
     */
    private $currencyService;

    /**
     * @var FormulaViewService
     */
    private $formulaViewService;

    public function __construct(
        CaseFinanceService $caseFinanceService,
        FormulaResultService $formulaResultService,
        FormulaViewService $formulaViewService,
        CurrencyService $currencyService
    ) {
        $this->caseFinanceService = $caseFinanceService;
        $this->formulaResultService = $formulaResultService;
        $this->formulaViewService = $formulaViewService;
        $this->currencyService = $currencyService;
    }

    /**
     * @param Accident $accident
     * @param array $types
     * @return Collection
     * @throws InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function get(Accident $accident, array $types): Collection
    {
        $financeDataCollection = collect([]);

        $types = array_intersect($types, self::FINANCE_TYPES);
        foreach ($types as $type) {
            switch ($type) {
                case 'income':
                    $data = $this->getIncomeData($accident);
                    break;
                case 'assistant':
                    $data = $this->getAssistantData($accident);
                    break;
                case 'caseable':
                    $data = $this->getCaseableData($accident);
                    break;
                default:
                    throw new InconsistentDataException('Undefined finance type');
            }
            $typeResult = collect([
                'type' => $type,
                'loading' => false,
                'payment' => $data['payment'],
                'currency' => $data['payment'] ? $data['payment']->currency : $this->currencyService->getDefaultCurrency(),
                'calculatedValue' => array_key_exists('formula', $data) ? $this->formulaResultService->calculate($data['formula']) : 0,
                'formulaView' => array_key_exists('formula', $data) ? $this->formulaViewService->render($data['formula']) : 'fixed',
            ]);
            $financeDataCollection->push($typeResult);
        }

        return $financeDataCollection;
    }

    /**
     * @param Accident $accident
     * @return array
     * @throws InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    private function getIncomeData(Accident $accident): array
    {
        $payment = $accident->incomePayment;
        // calculate only if payment isn't fixed
        if (!$payment || !$payment->fixed) {
            $caseableData = $this->getCaseableData($accident);
            $caseableValue = $this->getValueFromData($caseableData);

            $formula = $this->caseFinanceService->newFormula();
            // I need to sub 2 different results instead of sub formula builders
            // to not get 1. big formula 2. data inconsistencies
            $assistantData = $this->getAssistantData($accident);
            $assistantValue = $this->getValueFromData($assistantData);

            // use in formula
            $formula
                ->subFloat($assistantValue)
                ->subFloat($caseableValue);
        }
        return compact('payment', 'formula');
    }

    /**
     * @param array $data
     * @return float
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    private function getValueFromData(array $data): float
    {
        $res = 0;
        if ($data['payment'] && $data['payment'] instanceof Payment && $data['payment']->fixed) {
            $res = $data['payment']->value;
        } elseif (array_key_exists('formula', $data)) {
            $res = $this->formulaResultService->calculate($data['formula']);
        }

        return $res;
    }

    /**
     * @param $accident
     * @return array
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    private function getAssistantData($accident): array
    {
        $payment = $accident->paymentFromAssistant;
        if (!$payment || !$payment->fixed) {
            $formula = $this->caseFinanceService->getFromAssistantFormula($accident);
        }
        return compact('payment', 'formula');
    }

    /**
     * @param Accident $accident
     * @return array
     * @throws InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    private function getCaseableData(Accident $accident): array
    {
        $payment = $accident->paymentToCaseable;
        if (!$payment || !$payment->fixed) {
            $formula = $accident->isDoctorCaseable()
                ? $this->caseFinanceService->getToDoctorFormula($accident)
                : $this->caseFinanceService->getToHospitalFormula($accident);
        }
        return compact('payment', 'formula');
    }
}
