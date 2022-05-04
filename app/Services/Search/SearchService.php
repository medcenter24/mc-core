<?php
/*
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
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Search;

use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Search\Model\Query\Loader\SearchQueryFieldLoader;
use medcenter24\mcCore\App\Services\Search\Model\Query\Loader\SearchQueryFilterLoader;
use medcenter24\mcCore\App\Services\Search\Model\Query\SearchQueryBuilder;
use medcenter24\mcCore\App\Services\Search\Model\Query\SearchQueryFactory;

class SearchService
{
    use ServiceLocatorTrait;

    public function search(SearchRequest $searchRequest): Collection
    {
        $searchQuery = $this->getSearchQueryFactory()->create('accidents');

        $this->getSearchQueryFieldLoader()->load(
            $searchQuery,
            $searchRequest->getFields()->getFields(),
        );
        $this->getSearchQueryFilterLoader()->load(
            $searchQuery,
            $searchRequest->getFilters()->getFilters(),
        );

        $query = $this->getSearchQueryBuilder()->build($searchQuery);

//        \Illuminate\Support\Facades\Log::error('search query', [$query->toSql()]);
//        var_dump($query->toSql());die;
        // $q = $query->toSql();

        $data = $query->get();
        return $this->getSearchResultService()
            ->getResultData($data, $searchRequest);
    }

    private function getSearchQueryFactory(): SearchQueryFactory
    {
        return $this->getServiceLocator()->get(SearchQueryFactory::class);
    }

    private function getSearchQueryFieldLoader(): SearchQueryFieldLoader
    {
        return $this->getServiceLocator()->get(SearchQueryFieldLoader::class);
    }

    private function getSearchQueryFilterLoader(): SearchQueryFilterLoader
    {
        return $this->getServiceLocator()->get(SearchQueryFilterLoader::class);
    }

    private function getSearchQueryBuilder(): SearchQueryBuilder
    {
        return $this->getServiceLocator()->get(SearchQueryBuilder::class);
    }

    private function getSearchResultService(): SearchResultService
    {
        return $this->getServiceLocator()->get(SearchResultService::class);
    }
}
