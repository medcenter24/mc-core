<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Entity;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\DatePeriod;
use medcenter24\mcCore\App\Entity\DatePeriodInterpretation;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Entity\FinanceCondition;
use medcenter24\mcCore\App\Entity\FinanceStorage;
use medcenter24\mcCore\App\Services\Finance\FinanceBaseConditionService;

class FinanceConditionService extends AbstractModelService
{
    public const FIELD_CREATED_BY = 'created_by';
    public const FIELD_TITLE = 'title';
    public const FIELD_VALUE = 'value';
    public const FIELD_TYPE = 'type';
    public const FIELD_CURRENCY_ID = 'currency_id';
    public const FIELD_CURRENCY_MODE = 'currency_mode';
    public const FIELD_MODEL = 'model';
    public const FIELD_ORDER = 'order';

    public const FILLABLE = [
        self::FIELD_CREATED_BY,
        self::FIELD_TITLE,
        self::FIELD_VALUE,
        self::FIELD_TYPE,
        self::FIELD_CURRENCY_ID,
        self::FIELD_CURRENCY_MODE,
        self::FIELD_MODEL,
        self::FIELD_ORDER,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_CREATED_BY,
        self::FIELD_TITLE,
        self::FIELD_VALUE,
        self::FIELD_TYPE,
        self::FIELD_CURRENCY_ID,
        self::FIELD_CURRENCY_MODE,
        self::FIELD_MODEL,
        self::FIELD_ORDER,
    ];

    public const UPDATABLE = [
        self::FIELD_CREATED_BY,
        self::FIELD_TITLE,
        self::FIELD_VALUE,
        self::FIELD_TYPE,
        self::FIELD_CURRENCY_ID,
        self::FIELD_CURRENCY_MODE,
        self::FIELD_MODEL,
        self::FIELD_ORDER,
    ];

    /** @var string Types */
    public const PARAM_TYPE_ADD = 'add';
    public const PARAM_TYPE_SUBTRACT = 'sub';
    public const PARAM_TYPE_BASE = 'base';

    /** @var string Currency modes */
    public const PARAM_CURRENCY_MODE_PERCENT = 'percent';
    public const PARAM_CURRENCY_MODE_CURRENCY = 'currency';

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return FinanceCondition::class;
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        self::FIELD_CREATED_BY => "int",
        self::FIELD_TITLE => "string",
        self::FIELD_VALUE => "int",
        self::FIELD_TYPE => "string",
        self::FIELD_CURRENCY_ID => "int",
        self::FIELD_CURRENCY_MODE => "string",
        self::FIELD_MODEL => "string",
        self::FIELD_ORDER => "int"])]
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_CREATED_BY => 0,
            self::FIELD_TITLE => '',
            self::FIELD_VALUE => 0,
            self::FIELD_TYPE => '',
            self::FIELD_CURRENCY_ID => 0,
            self::FIELD_CURRENCY_MODE => '',
            self::FIELD_MODEL => '',
            self::FIELD_ORDER => 0,
        ];
    }

    /**
     * Types
     * @return array
     */
    public function getTypes(): array
    {
        return [
            self::PARAM_TYPE_ADD,
            self::PARAM_TYPE_SUBTRACT,
            self::PARAM_TYPE_BASE,
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
     * ]
     *
     * @return Collection
     */
    public function findConditions(string $model, array $filters = []): Collection
    {
        $transformedFilters = $this->getFilters($filters);
        return $this->find($model, $transformedFilters);
    }

    /**
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

                case Service::class :
                    // only arrays allowed
                    if (is_array($val) && !empty($val)) {
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
        // \Illuminate\Support\Facades\DB::enableQueryLog();

        $storedConditions = $this->getStoredConditions($filters);

        // $query = \Illuminate\Support\Facades\DB::getQueryLog();
        $conditions = FinanceCondition::query()
            ->where(static function (Builder $q) use ($model, $storedConditions) {
                // founded by stored conditions
                $q->where('model', $model)
                    ->whereIn('id', array_keys($storedConditions));
            })->orWhere(static function (Builder $q) use ($model) {
                // general conditions for the $model (without stored conditions)
                $q->where('model', $model)->doesntHave('conditions');
            })
            ->withCount('conditions')
            ->orderByDesc('order')
            ->orderBy('id')
            ->get();

        return $this->filterConditions($conditions, $storedConditions);
    }

    /**
     * Searching by model with a filters
     * @param array $filters
     * @return Collection
     */
    public function search(array $filters = []): Collection
    {
        return $this->getQuery($filters)
            ->orderByDesc(self::FIELD_ORDER)
            ->get();
    }

    /**
     * Returns array of conditions with count of matches (array(1 => 10))
     * @param Collection $filters
     * @return array
     */
    private function getStoredConditions(Collection $filters): array
    {
        $storedConditions = [];
        if ($filters->count()) {
            $storageQuery = FinanceStorage::query();
            $filters->each(static function ($val, $key) use ($storageQuery) {
                $storageQuery->orWhere(static function (Builder $query) use ($val, $key) {
                    $query->where('model', $key);
                    if (is_array($val)) {
                        $query->whereIn('model_id', $val);
                    } else {
                        $query->where('model_id', $val);
                    }
                });
            });

            $storedConditionsData = $storageQuery
                ->get(['finance_condition_id'])
                ->toArray();

            foreach ($storedConditionsData as $storedCondition) {
                $id = (int)$storedCondition['finance_condition_id'];
                if (!array_key_exists($id, $storedConditions)) {
                    $storedConditions[$id] = 1;
                } else {
                    $storedConditions[$id]++;
                }
            }
        }
        return $storedConditions;
    }

    private function filterConditions(Collection $conditions, array $matches): Collection
    {
        return $this->getServiceLocator()
            ->get(FinanceBaseConditionService::class)
            ->filterBaseCondition($conditions, $matches);
    }
}
