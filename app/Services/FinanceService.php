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
    public function getDefaultCurrency()
    {
        return new FinanceCurrency();
    }

    /**
     * @param Payment $payment
     * @return float
     */
    public function convertToDefault(Payment $payment)
    {
        return $payment->getAttribute('currency_id') == $this->getDefaultCurrency()->id
                ? $payment->value
                : $this->convert($payment, $this->getDefaultCurrency());
    }

    /**
     * @param Payment $payment
     * @param FinanceCurrency $currency
     * @return float
     */
    public function convert(Payment $payment, FinanceCurrency $currency)
    {
        // todo implement Exchange Rates if/when needed
        return $payment->value;
    }
}
