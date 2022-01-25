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

namespace medcenter24\mcCore\App\Services\ApiSearch;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use medcenter24\mcCore\App\Models\Database\Filter as PredefinedFilter;
use medcenter24\mcCore\App\Models\Database\Relation;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Paginator;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Sorter;
use medcenter24\mcCore\App\Services\Core\Http\DataLoaderRequestBuilder;
use medcenter24\mcCore\App\Services\Core\Http\Filter\RequestBuilderFilterTransformer;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use Illuminate\Support\Collection;

class ApiSearchService
{
    use ServiceLocatorTrait;

    private ?SearchFieldLogic $searchFieldLogicService = null;
    private array $joined = [];
    private ?DataLoaderRequestBuilder $requestBuilder = null;

    /**
     * @param Request $request
     * @return Paginator
     */
    private function getPaginator(Request $request): Paginator
    {
        /** @var Paginator $paginator */
        $paginator = $this->getServiceLocator()->get(Paginator::class)->create();
        $paginator->inject($request->json(DataLoaderRequestBuilder::PAGINATOR, []));
        return $paginator;
    }

    /**
     * @param Request $request
     * @return Sorter
     */
    private function getSorter(Request $request): Sorter
    {
        /** @var Sorter $sorter */
        $sorter = $this->getServiceLocator()->get(Sorter::class)->create();
        $sorter->inject($request->json(DataLoaderRequestBuilder::SORTER, []));
        return $sorter;
    }

    /**
     * @param Request $request
     * @return Filter
     */
    private function getFilter(Request $request): Filter
    {
        /** @var Filter $filter */
        $filter = $this->getServiceLocator()->get(Filter::class)->create();
        $filter->inject($request->json(DataLoaderRequestBuilder::FILTER, []));
        return $filter;
    }

    /**
     * @return RequestBuilderFilterTransformer
     */
    private function getFilterTransformer(): RequestBuilderFilterTransformer
    {
        return $this->getServiceLocator()->get(RequestBuilderFilterTransformer::class);
    }

    /**
     * Logic to work with data provided by fields
     * @return SearchFieldLogic
     */
    private function getFieldLogic(): SearchFieldLogic
    {
        if (!$this->searchFieldLogicService) {
            $this->searchFieldLogicService = $this->getServiceLocator()->get(SearchFieldLogic::class);
        }
        return $this->searchFieldLogicService;
    }

    /**
     * To Replace default SearchFieldLogic with specified
     * @param SearchFieldLogic|null $service
     */
    public function setFieldLogic(SearchFieldLogic $service = null) : void
    {
        $this->searchFieldLogicService = $service;
        $this->getFieldLogic(); // re-init if null
    }

    /**
     * From the 1 filter value could be generated more filters
     *
     * @example
     *  date range 'd1 - d2' will be generated filter with d > d1 and d < d2
     *
     * @param $filter
     * @return array
     */
    private function getInternalFilter($filter): array
    {
        // general transformer
        $internalFilter = $this->getFilterTransformer()->transform($filter);

        // internal transformer
        return $this->getFieldLogic()->transformFieldToInternalFormat($internalFilter);
    }

    /**
     * join tables if required info
     * @param Builder $eloquent
     */
    private function joinRelations(Builder $eloquent): void
    {
        $self = $this;
        $relations = $this->getFieldLogic()->getRelations();
        $relations->each(static function (Relation $relation) use ($self, $eloquent) {
            if (!in_array($relation->getTable(), $self->joined, true)) {
                $eloquent->join(
                    $relation->getTable(),
                    $relation->getFirst(),
                    $relation->getOperator(),
                    $relation->getSecond(),
                    $relation->getType(),
                    $relation->getWhere()
                );
                $self->joined[] = $relation->getTable();
            }
        });
    }

    /**
     * adding where to the eloquent
     * @param Builder $eloquent
     * @param array $filter
     */
    private function whereClause(Builder $eloquent, array $filter): void
    {
        switch ($filter[Filter::FIELD_MATCH]) {
            case Filter::MATCH_BETWEEN:
                $eloquent->whereBetween($filter[Filter::FIELD_NAME], $filter[Filter::FIELD_VALUE]);
                break;
            case Filter::MATCH_IN:
                $eloquent->whereIn($filter[Filter::FIELD_NAME], $filter[Filter::FIELD_VALUE]);
                break;
            case 'ilike':
                if (config('database.default') === 'pgsql') {
                    $eloquent->where($filter[Filter::FIELD_NAME], $filter[Filter::FIELD_MATCH],
                        $filter[Filter::FIELD_VALUE]);
                } else {
                    $eloquent->where($filter[Filter::FIELD_NAME], 'like', $filter[Filter::FIELD_VALUE]);
                }
                break;
            default:
                $eloquent->where($filter[Filter::FIELD_NAME], $filter[Filter::FIELD_MATCH],
                    $filter[Filter::FIELD_VALUE]);
        }
    }

    /**
     * Attach filter to the query
     * @param Builder $eloquent
     * @param Collection $filters
     */
    private function attachFilter(Builder $eloquent, Collection $filters): void
    {
        $self = $this;
        $filters->each(static function (array $filter) use ($eloquent, $self) {
            $filter = $self->getInternalFilter($filter);
            $self->whereClause($eloquent, $filter);
        });

        // and predefined filters for all queries:
        $this->getFieldLogic()
            ->getFilters()
            ->each(static function (PredefinedFilter $filter) use ($eloquent, $self) {
                $self->whereClause($eloquent, $filter->asArray());
        });
    }

    /**
     * @param Builder $eloquent
     * @param Collection $sorter
     */
    private function attachSort(Builder $eloquent, Collection $sorter): void
    {
        $self = $this;
        $sorter->each(static function (array $sortField) use ($self, $eloquent) {
            $sortField = $self->getFieldLogic()->transformFieldToInternalFormat($sortField);
            $eloquent->orderBy($sortField[Filter::FIELD_NAME], $sortField[Filter::FIELD_VALUE]);
        });
    }

    private function getRequestBuilder(Request $request)
    {
        if (!$this->requestBuilder) {
            $this->requestBuilder = $this->getServiceLocator()->get(DataLoaderRequestBuilder::class);
            $this->requestBuilder->setPaginator($this->getPaginator($request));
            $this->requestBuilder->setSorter($this->getSorter($request));
            $this->requestBuilder->setFilter($this->getFilter($request));
        }
        return $this->requestBuilder;
    }

    private function getEloquent(Request $request, Model $model): Builder
    {
        $eloquent = $model->newQuery();

        $this->joinRelations($eloquent);
        $this->attachFilter($eloquent, $this->getRequestBuilder($request)->getFilter()->getFilters());
        $this->attachSort($eloquent, $this->getRequestBuilder($request)->getSorter()->getSortBy());

        return $eloquent;
    }

    /**
     * @param Request $request
     * @param Model $model
     * @return LengthAwarePaginator
     */
    public function search(Request $request, Model $model): LengthAwarePaginator
    {
        return $this->getEloquent($request, $model)->paginate(
            $this->getRequestBuilder($request)->getPaginator()->getLimit(),
            // working with models, other info will be got later
            [$model->getTable() . '.*'],
            'page',
            $this->getRequestBuilder($request)->getPage()
        );
    }

    public function getCollection(Request $request, Model $model): Collection
    {
        return $this->getEloquent($request, $model)->get([$model->getTable() . '.*']);
    }
}
