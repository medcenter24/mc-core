<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Accident;
use App\DatePeriod;
use App\Doctor;
use App\DoctorService;
use App\FinanceStorage;
use Illuminate\Support\Collection;

class FinanceConditionService
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

    /**
     *
     * @param array $models
     *  [
     *      Doctor::class => 1
     *      DatePeriod::class => Carbon,
     *      DoctorAccident::class => Services...
     * ]]
     *
     * @return array
     */
    public static function findConditions($models = [])
    {
        $result = [];
        if (count($models)) {
            $storageQuery = FinanceStorage::query();
            $datePeriod = false;
            foreach ($models as $key => $val) {
                if ($key === DatePeriod::class) {
                    // dates will be filtered later, on the sql results
                    $datePeriod = $val;
                } elseif ($key === DoctorService::class) {
                    if ($val && is_array($val)) {
                        $storageQuery->whereIn($key, $val);
                    }
                } else {
                    $storageQuery->where('model', $key)->where('model_id', $val);
                }
            }
            // todo неправильнывй формат хранения периодов дат, не понятно как их теперь сравнить чисто по бд?
            $result = $storageQuery
                ->groupBy('finance_condition_id')
                ->get();

            // filter by the date
            if ($datePeriod) {
                var_dump($result, $datePeriod); die('not implemented yet');
            }
        }

        return $result;
    }
}
