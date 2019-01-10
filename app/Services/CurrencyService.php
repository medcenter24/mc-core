<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Exceptions\InconsistentDataException;
use App\FinanceCurrency;

class CurrencyService
{
    /**
     * @var FinanceCurrency
     */
    private $defaultCurrency;

    /**
     * Converts value to the other currencies
     * @param int $val
     * @param FinanceCurrency $currency
     * @param FinanceCurrency|null $toCurrency
     * @return int
     * @throws InconsistentDataException
     */
    public function convertCurrency($val, FinanceCurrency $currency, FinanceCurrency $toCurrency = null)
    {
        if (isset($toCurrency) && $toCurrency->getAttribute('code') !== $currency->getAttribute('code')) {
            throw new InconsistentDataException('Currency Convert has not been implemented yet');
        }
        // when it will be more supported currencies - needs to be implemented CurrencyConverterService
        return $val;
    }

    public function getDefaultCurrency(): FinanceCurrency
    {
        if (!$this->defaultCurrency) {
            $this->defaultCurrency = FinanceCurrency::firstOrFail();
        }

        return $this->defaultCurrency;
    }
}
