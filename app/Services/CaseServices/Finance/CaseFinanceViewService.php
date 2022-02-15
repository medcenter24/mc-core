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

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\CaseServices\Finance;

use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Contract\Formula\FormulaBuilder;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Models\Formula\Exception\FormulaException;
use medcenter24\mcCore\App\Entity\Payment;
use medcenter24\mcCore\App\Services\Entity\CurrencyService;
use medcenter24\mcCore\App\Services\Entity\PaymentService;
use medcenter24\mcCore\App\Services\Formula\FormulaResultService;
use medcenter24\mcCore\App\Services\Formula\FormulaViewService;
use Illuminate\Support\Collection;
use Throwable;
use function Webmozart\Assert\Tests\StaticAnalysis\null;

class CaseFinanceViewService
{
    public const FINANCE_TYPES = ['income', 'assistant', 'caseable'];

    private CaseFinanceService $caseFinanceService;
    private FormulaResultService $formulaResultService;
    private CurrencyService $currencyService;
    private FormulaViewService $formulaViewService;

    public function __construct(
        CaseFinanceService   $caseFinanceService,
        FormulaResultService $formulaResultService,
        FormulaViewService   $formulaViewService,
        CurrencyService      $currencyService
    )
    {
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
     * @throws FormulaException
     * @throws Throwable
     */
    public function get(Accident $accident, array $types): Collection
    {
        $financeDataCollection = collect([]);

        $types = array_intersect($types, self::FINANCE_TYPES);
        foreach ($types as $type) {
            $data = match ($type) {
                'income' => $this->getIncomeData($accident),
                'assistant' => $this->getAssistantData($accident),
                'caseable' => $this->getCaseableData($accident),
                default => throw new InconsistentDataException('Undefined finance type'),
            };

            $currency = $this->hasPayment($data)
                ? $this->getPayment($data)->currency
                : $this->currencyService->getDefaultCurrency();

            $typeResult = collect([
                'type'             => $type,
                'loading'          => false,
                'payment'          => $data['payment'],
                'currency'         => $currency,
                'calculatedValue'  => $this->getCalculatedValue($data),
                'formulaView'      => array_key_exists('formula', $data) && $data['formula'] instanceof FormulaBuilder
                    ? $this->formulaViewService->render($data['formula']) : $data['formula'],
                'view'             => $this->getFinalActiveValue($data) . ' ' . $currency->code,
                'finalActiveValue' => $this->getFinalActiveValue($data),
            ]);
            $financeDataCollection->push($typeResult);
        }

        return $financeDataCollection;
    }

    private function getFinalActiveValue(array $data): string
    {
        /** @var Payment $payment */
        $payment = $data['payment'];
        if ($payment && (int)$payment->getAttribute(PaymentService::FIELD_FIXED)) {
            $val = $payment->getAttribute(PaymentService::FIELD_VALUE);
        } else {
            try {
                $val = $this->getCalculatedValue($data);
            } catch (FormulaException $e) {
                Log::error('Formula can not be calculated', [$e]);
                $val = 'formula_error';
            }
        }

        return (string)$val;
    }

    /**
     * @param array $data
     * @return float
     * @throws FormulaException
     */
    private function getCalculatedValue(array $data): float
    {
        $value = 0;
        if (array_key_exists('formula', $data)) {
            if ($data['formula'] instanceof FormulaBuilder) {
                $value = $this->formulaResultService->calculate($data['formula']);
            } elseif ($data['formula'] === 'invoice' && $this->hasPayment($data)) {
                $value = $this->getPayment($data)->getAttribute(PaymentService::FIELD_VALUE);
            }
        }
        return (float)$value;
    }

    private function hasPayment(array $data): bool
    {
        return array_key_exists('payment', $data) && $data['payment'] instanceof Payment;
    }

    private function getPayment(array $data): ?Payment
    {
        $payment = null;
        if ($this->hasPayment($data)) {
            $payment = $data['payment'];
        }
        return $payment;
    }

    /**
     * @param Accident $accident
     * @return array
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    private function getIncomeData(Accident $accident): array
    {
        $payment = $accident->incomePayment;
        // income can't be an invoice
        $formula = 'fixed';
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
     * @throws FormulaException
     */
    private function getValueFromData(array $data): float
    {
        $res = 0;
        if (
            $data['payment']
            && $data['payment'] instanceof Payment
            && ($data['payment']->fixed || (array_key_exists('formula', $data) && $data['formula'] === 'invoice'))
        ) {
            $res = $data['payment']->value;
        } elseif (array_key_exists('formula', $data) && $data['formula'] instanceof FormulaBuilder) {
            $res = $this->formulaResultService->calculate($data['formula']);
        }

        return (float)$res;
    }

    /**
     * @param $accident
     * @return array
     * @throws FormulaException
     */
    private function getAssistantData(Accident $accident): array
    {
        $payment = $accident->paymentFromAssistant;
        $formula = 'fixed';
        if (!$payment || !$payment->fixed) {
            $payment = $this->getAssistantInvoice($accident);
            $formula = 'invoice';
            if (!$payment) {
                $formula = $this->caseFinanceService->getFromAssistantFormula($accident);
            }
        }
        return compact('payment', 'formula');
    }

    private function getAssistantInvoice(Accident $accident)
    {
        $invoice = null;
        if ($accident->assistantInvoice
            && $accident->assistantInvoice->payment) {

            $invoice = $accident->assistantInvoice->payment;
        }
        return $invoice;
    }

    /**
     * @param Accident $accident
     * @return array
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    private function getCaseableData(Accident $accident): array
    {
        $invoicePayment = null;

        // 1. If saved fixed payment to caseable
        $formula = 'fixed';
        /** @var Payment $paymentToCaseable */
        $paymentToCaseable = $accident->getAttribute('paymentToCaseable');
        $payment = $paymentToCaseable;
        $hasFixedPayment = $paymentToCaseable && $paymentToCaseable->getAttribute(PaymentService::FIELD_FIXED);

        // 2. Or If case has Invoice payment
        if (!$hasFixedPayment) {
            $formula = 'invoice';
            // invoice has minor priority
            $invoicePayment = $this->getCaseableInvoice($accident);
            $payment = $invoicePayment;
        }

        // 3. Get formula if not fixed and no invoice payment
        if(!$hasFixedPayment && !$invoicePayment) {
            // it's possible that accident doesn't have caseable (new one)
            if ($accident->getAttribute('caseable')) {
                $formula = $accident->isDoctorCaseable()
                    ? $this->caseFinanceService->getToDoctorFormula($accident)
                    : $this->caseFinanceService->getToHospitalFormula($accident);
            } else {
                $formula = 0.00;
                $payment = null;
            }
        }

        return compact('payment', 'formula');
    }

    private function getCaseableInvoice(Accident $accident)
    {
        $invoice = null;
        if ($accident->isDoctorCaseable()
            && $accident->caseable
            && $accident->caseable->doctorInvoice
            && $accident->caseable->doctorInvoice->payment) {

            $invoice = $accident->caseable->doctorInvoice->payment;
        }
        if ($accident->isHospitalCaseable()
            && $accident->caseable
            && $accident->caseable->hospitalInvoice
            && $accident->caseable->hospitalInvoice->payment) {

            $invoice = $accident->caseable->hospitalInvoice->payment;
        }
        return $invoice;
    }
}
