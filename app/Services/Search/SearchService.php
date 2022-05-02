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
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Search\Model\Query\Loader\SearchQueryFieldLoader;
use medcenter24\mcCore\App\Services\Search\Model\Query\Loader\SearchQueryFilterLoader;
use medcenter24\mcCore\App\Services\Search\Model\Query\SearchQueryBuilder;
use medcenter24\mcCore\App\Services\Search\Model\Query\SearchQueryFactory;

class SearchService
{
    use ServiceLocatorTrait;

    public const FIELD_NPP = 'npp'; // todo add this field after the result responsed
    public const FIELD_PATIENT = 'patient';
    public const FIELD_CITY = 'city';
    public const FIELD_DOCTOR_INCOME = 'doctor-income';
    public const FIELD_ASSIST_REF_NUM = 'assist-ref-num';

    public const FIELDS_DB = [
        self::FIELD_PATIENT,
        self::FIELD_CITY,
        self::FIELD_DOCTOR_INCOME,
        self::FIELD_ASSIST_REF_NUM,
    ];


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

        Log::error('search query', [$query->toSql()]);
//        var_dump($query->toSql());die;

        return $query->get();
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
}
