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

namespace medcenter24\mcCore\App\Services;


use Illuminate\Database\Eloquent\Builder;
use medcenter24\mcCore\App\Helpers\Arr;
use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;

abstract class AbstractModelService
{
    use ServiceLocatorTrait;

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
    abstract protected function getRequiredFields(): array;

    /**
     * Extend data with default data (for undeclared vars only)
     * @param array $data
     * @return array
     */
    protected function appendRequiredData(array $data = []): array
    {
        foreach ($this->getRequiredFields() as $key => $val) {
            Arr::setDefault($data, $key, $val);
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
        return call_user_func([$this->getClassName(), 'create'], $this->appendRequiredData($data));
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
     * @param array $filters
     * @return Model|null
     */
    public function first(array $filters = []): ?Model
    {
        return $this->getQuery($filters)->first();
    }

    private function getQuery(array $filters = []): Builder
    {
        // I can't extend filters filters have to be correct from request
        // $filters = $this->appendRequiredData($filters);
        /** @var Builder $query */
        $query = call_user_func([$this->getClassName(), 'query']);
        foreach ($filters as $key => $filter) {
            $query->where($key, $filter);
        }
        return $query;
    }

    public function count(array $filters = []): int
    {
        return $this->getQuery($filters)->count();
    }
}
