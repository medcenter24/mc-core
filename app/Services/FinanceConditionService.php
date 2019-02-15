<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Assistant;
use App\DatePeriod;
use App\DatePeriodInterpretation;
use App\Doctor;
use App\DoctorService;
use App\FinanceCondition;
use App\FinanceStorage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FinanceConditionService
{
    /** @var string Types */
    public const PARAM_TYPE_ADD = 'add';
    public const PARAM_TYPE_SUBTRACT = 'sub';

    /** @var string Currency modes */
    public const PARAM_CURRENCY_MODE_PERCENT = 'percent';
    public const PARAM_CURRENCY_MODE_CURRENCY = 'currency';

    /**
     * Types
     * @return array
     */
    public function getTypes(): array
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
    public function getModes(): array
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
    public function allowedModels(): array
    {
        return [
            Assistant::class,
            Doctor::class,
        ];
    }

    /**
     *
     * @param string $model - allowedModels
     * @param array $filters
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
    public function findConditions($model, $filters = []): Collection
    {
        $transformedFilters = $this->getFilters($filters);
        return $this->find($model, $transformedFilters);
    }

    /**\
     * @param array $models
     * @return Collection
     */
    private function getFilters(array $models = []): Collection
    {
        $filters = collect();
        foreach ($models as $model => $val) {
            if (!$val) {
                continue;
            }

            switch ($model) {
                // looking for periods where current time covered
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
                        $filters->put($model, $periodIds->get('date_period_id'));
                    }

                    break;

                case DoctorService::class :
                    // only arrays allowed
                    if ($val && is_array($val) && count($val)) {
                        $filters->put($model, $val);
                    }

                    break;
                default:
                    $filters->put($model, $val);
            }
        }

        return $filters;
    }

    /**
     * @param string $model
     * @param Collection $filters
     * @return Collection
     */
    private function find(string $model, Collection $filters): Collection
    {
        if ($filters->count()) {

            // \Illuminate\Support\Facades\DB::enableQueryLog();

            /** @var \Illuminate\Database\Eloquent\Builder $storageQuery */
            $storageQuery = FinanceStorage::query();
            $filters->each(function ($val, $key) use ($storageQuery) {
                $storageQuery->orWhere(function(Builder $query) use ($val, $key) {
                    $query->where('model', $key);
                    if (is_array($val)) {
                        $query->whereIn('model_id', $val);
                    } else {
                        $query->where('model_id', $val);
                    }
                });
            });

            $storedConditions = $storageQuery
                ->groupBy('finance_condition_id')
                ->get(['finance_condition_id']);

            // $query = \Illuminate\Support\Facades\DB::getQueryLog();
            $conditions = FinanceCondition::query()
                ->where(function ($q) use ($model, $storedConditions) {
                    // founded by stored conditions
                    $q->where('model', $model)
                        ->whereIn('id', $storedConditions);
                })->orWhere( function ($q) use ($model) {
                    // general conditions for the $model (without stored conditions)
                    $q->where('model', $model)->doesntHave('conditions');
                })
                ->withCount('conditions')
                ->get();
        } else {
            $conditions = collect();
        }

        return $conditions;
    }
}
