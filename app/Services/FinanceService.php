<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Accident;
use App\Doctor;

class FinanceService
{
    /** @var string Types */
    const PARAM_TYPE_ADD = 'add';
    const PARAM_TYPE_SUBTRACT = 'sub';

    /** @var string Currency modes */
    const PARAM_CURRENCY_MODE_PERCENT = 'percent';
    const PARAM_CURRENCY_MODE_CURRENCY = 'currency';

    /**
     * Types
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::PARAM_TYPE_ADD,
            self::PARAM_TYPE_SUBTRACT,
        ];
    }

    /**
     * Modes
     * @return array
     */
    public static function getModes()
    {
        return [
            self::PARAM_CURRENCY_MODE_PERCENT,
            self::PARAM_CURRENCY_MODE_CURRENCY,
        ];
    }

    /**
     * Either
     * Counted for the accident (company profit from the accident)
     * Or
     * Counted for the doctor (doctors payment)
     *
     * @return array
     */
    public static function allowedModels()
    {
        return [
            Accident::class,
            Doctor::class,
        ];
    }
}
