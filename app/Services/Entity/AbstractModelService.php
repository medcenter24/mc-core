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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services\Entity;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\Arr;
use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;

abstract class AbstractModelService implements ModelService
{
    use ServiceLocatorTrait;

    public const FIELD_ID = 'id';

    public const FIELD_CREATED_AT = 'created_at';
    public const FIELD_DELETED_AT = 'deleted_at';
    public const FIELD_UPDATED_AT = 'updated_at';

    public const DATE_FIELDS = [
        self::FIELD_CREATED_AT,
        self::FIELD_DELETED_AT,
        self::FIELD_UPDATED_AT,
    ];

    public const UPDATABLE = [];
    public const FILLABLE = [];
    public const VISIBLE = [];
    public const HIDDEN = [];

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

    /**
     * @param array $keys
     * @param array $data
     * @return array
     */
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
     * @return bool
     */
    private function updateModel(Model $model, array $data): bool
    {
        $data = $this->filterWith(static::UPDATABLE, $data);
        foreach ($data as $key => $val) {
            $model->setAttribute($key, $val);
        }
        return $model->save();
    }

    /**
     * @param array $filterByFields
     * @param array $data
     * @return Model
     * @throws InconsistentDataException
     */
    public function findAndUpdate(array $filterByFields, array $data): Model
    {
        $filter = [];
        foreach ($filterByFields as $val) {
            if (!array_key_exists($val, $data)) {
                throw new InconsistentDataException('Required field "'.$val.'" not defined in the provided data');
            }
            $filter[$val] = $data[$val];
        }

        $obj = $this->first($filter);
        if (!$obj) {
            throw new InconsistentDataException('Object not found');
        }

        if (!$this->updateModel($obj, $data)) {
            throw new InconsistentDataException('Model can not be updated');
        }
        return $obj;
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

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        $className = $this->getClassName();
        return new $className();
    }

    /**
     * @param array $data
     */
    protected function convertEmptyDatesToNull(array &$data): void
    {
        $modelDates = $this->getModel()->getDates();
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

    /**
     * @param $id
     * @return bool
     * @throws InconsistentDataException
     */
    public function delete($id): bool
    {
        /** @var Model $obj */
        $obj = $this->first(['id' => $id]);
        if (!$obj) {
            throw new InconsistentDataException('Object not found');
        }
        try {
            return $obj->delete();
        } catch (Exception $e) {
            throw new InconsistentDataException($e->getMessage());
        }
    }
}
