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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Helpers\Arr;
use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;

abstract class AbstractModelService
{
    use ServiceLocatorTrait;

    public const FIELD_ID = 'id';

    public const FIELD_CREATED_AT = 'created_at';
    public const FIELD_DELETED_AT = 'created_at';
    public const FIELD_UPDATED_AT = 'created_at';

    public const DATE_FIELDS = [
        self::FIELD_CREATED_AT,
        self::FIELD_DELETED_AT,
        self::FIELD_UPDATED_AT,
    ];

    public const UPDATABLE = [];
    public const FILLABLE = [];
    public const VISIBLE = [];

    /**
     * Name of the Model (ex: City::class)
     * @return string
     */
    abstract protected function getClassName(): string;

    /**
     * Initialize defaults to avoid database exceptions
     * (different storage have different rules, so it is correct to set defaults instead of nothing)
     * @return array
     */
    abstract protected function getFillableFieldDefaults(): array;

    /**
     * Extend data with default data (for undeclared vars only)
     * @param array $data
     * @return array
     */
    protected function appendRequiredData(array $data = []): array
    {
        foreach ($this->getFillableFieldDefaults() as $key => $val) {
            Arr::setDefault($data, $key, $val);
        }
        return $data;
    }

    private function filterWith(array $keys, array $data): array
    {
        foreach ($data as $key => $val) {
            if (!in_array($key, $keys, true)) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    /**
     * Model::create
     * @param array $data
     * @return Model
     */
    public function create(array $data = []): Model
    {
        $data = $this->filterWith(static::FILLABLE, $data);
        $data = $this->appendRequiredData($data);
        return call_user_func([$this->getClassName(), 'create'], $data);
    }

    /**
     * Update model with data
     * @param Model $model
     * @param array $data
     * @return void
     */
    public function updateModel(Model $model, array $data): void
    {
        $data = $this->filterWith(static::UPDATABLE, $data);
        foreach ($data as $key => $val) {
            $model->setAttribute($key, $val);
        }
        $model->save();
    }

    /**
     * Model::firstOrCreate
     * @param array $data
     * @return Model
     */
    public function firstOrCreate(array $data = []): Model
    {
        // don't need to set required data when looking for data
        $obj = $this->first($data);
        if (!$obj) {
            $obj = $this->create($data);
        }
        return $obj;
    }

    protected function convertEmptyDatesToNull(array &$data): void
    {
        $className = $this->getClassName();
        /** @var Model $modelObj */
        $modelObj = new $className;
        $modelDates = $modelObj->getDates();
        if (is_array($modelDates)) {
            foreach ($data as $key => $item) {
                if ($item === '' && in_array($key, $modelDates, true)) {
                    $data[$key] = null;
                }
            }
        }
    }

    /**
     * @param array $filters
     * @return Model|null
     */
    public function first(array $filters = []): ?Model
    {
        return $this->getQuery($filters)->first();
    }

    /**
     * Searching by model with a filters
     * @param array $filters
     * @return Collection
     */
    public function search(array $filters = []): Collection
    {
        return $this->getQuery($filters)->get();
    }

    /**
     * @param array $filters
     * @return Builder
     */
    protected function getQuery(array $filters = []): Builder
    {
        // dates should be a null instead of empty string
        $this->convertEmptyDatesToNull($filters);

        /** @var Builder $query */
        $query = call_user_func([$this->getClassName(), 'query']);
        foreach ($filters as $key => $filter) {
            $query->where($key, $filter);
        }
        return $query;
    }

    /**
     * @param array $filters
     * @return int
     */
    public function count(array $filters = []): int
    {
        return $this->getQuery($filters)->count();
    }
}
