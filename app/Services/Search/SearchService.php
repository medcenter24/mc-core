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

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Search\Model\SearchField;
use medcenter24\mcCore\App\Services\Search\Model\SearchFieldsCollection;
use medcenter24\mcCore\App\Services\Search\Model\SearchFiltersCollection;

class SearchService
{
    use ServiceLocatorTrait;

    public function search(SearchRequest $searchRequest): Collection
    {
        $query = $this->createQuery();

        $fields = $searchRequest->getFields();
        $this->addColumns($query, $fields);

        $filters = $searchRequest->getFilters();
        $this->addFilters($query, $filters);

        return $query->get();
    }

    private function createQuery(): Builder
    {
        return DB::table('accidents');
    }

    private function addColumns(Builder $query, SearchFieldsCollection $fields): void
    {
        /** @var SearchField $field */
        foreach ($fields as $field) {
            $this->addField($query, $field);
        }
    }

    private function addField(Builder $query, SearchField $field): void
    {
        // select
        // join
        // order
    }

    private function addFilters(Builder $query, SearchFiltersCollection $filters): void
    {
        // where
    }
}
