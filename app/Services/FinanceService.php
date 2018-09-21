<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\FinanceCurrency;

class FinanceService
{
    /** @var string Types */
    const PARAM_TYPE_PAYMENT = 'payment';
    const PARAM_TYPE_DISCOUNT = 'discount';

    /** @var string Currencies */
    const PARAM_DEFAULT_CURRENCY = 'euro';
    /**
     * it isn't a currency from the currency table but can be calculated in conditions
     * TODO
     */
    const PARAM_CURRENCY_PERCENT = 'percent';

    public function getTypes()
    {
        return [
            FinanceService::PARAM_TYPE_PAYMENT,
            FinanceService::PARAM_TYPE_DISCOUNT,
        ];
    }
}
