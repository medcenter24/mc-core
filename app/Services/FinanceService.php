<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\FinanceCurrency;
use App\Payment;

class FinanceService
{
    /**
     * @return FinanceCurrency
     */
    public function getDefaultCurrency(): FinanceCurrency
    {
        return new FinanceCurrency();
    }

    /**
     * @param Payment $payment
     * @return float
     */
    public function convertToDefault(Payment $payment): float
    {
        return $payment->getAttribute('currency_id') === $this->getDefaultCurrency()->id
                ? $payment->value
                : $this->convert($payment, $this->getDefaultCurrency());
    }

    /**
     * @param Payment $payment
     * @param FinanceCurrency $currency
     * @return float
     */
    public function convert(Payment $payment, FinanceCurrency $currency): float
    {
        // todo implement Exchange Rates if/when needed
        return $payment->value;
    }
}
