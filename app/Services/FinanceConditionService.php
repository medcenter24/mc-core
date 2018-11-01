<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Accident;
use App\DatePeriod;
use App\DatePeriodInterpretation;
use App\Doctor;
use App\DoctorService;
use App\FinanceCondition;
use App\FinanceStorage;
use Carbon\Carbon;
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
    public function getTypes()
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
    public function getModes()
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
    public function allowedModels()
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
     *      DoctorAccident::class => 1,
     *      Assistant::class => 1,
     *      City::class => 1,
     *      DoctorService::class => [1, 2, 3, 4], // id's
     * ]]
     *
     * @return Collection
     */
    public function findConditions($models = [])
    {
        $result = collect([]);
        if (count($models)) {
            $storageQuery = FinanceStorage::query();
            foreach ($models as $key => $val) {
                switch ($key) {
                    case DatePeriod::class :
                        /** @var Carbon $date */
                        $date = $val;
                        $time = $date->toTimeString();
                        $periodIds = DatePeriodInterpretation::query()
                            ->where('day_of_week', $date->dayOfWeek)
                            ->where('from', '>=', $time)
                            ->where('to', '<=', $time)
                            ->get(['date_period_id']);
                        if ($periodIds->count()) {
                            $storageQuery->where('model', $key)->whereIn('model_id', $periodIds->get('date_period_id'));
                        }
                        break;
                    case DoctorService::class :
                        if ($val && is_array($val) && count($val)) {
                            $storageQuery->where('model', $key)->whereIn('model_id', $val);
                        }
                        break;
                    default:
                        $storageQuery->where('model', $key)->where('model_id', $val);
                }
            }

            $stored = $storageQuery
                ->groupBy('finance_condition_id')
                ->get('finance_condition_id');

            $result = FinanceCondition::query()->whereIn('id', $stored);
        }

        return $result;
    }
}
