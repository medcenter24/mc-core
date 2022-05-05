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

namespace medcenter24\mcCore\App\Services\Search\Model\Query\Loader;

use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Services\Search\Model\Filter\DbFilter\SearchDbFilter;
use medcenter24\mcCore\App\Services\Search\Model\Filter\DbFilter\SearchDbFilterFactory;
use medcenter24\mcCore\App\Services\Search\Model\Filter\Request\SearchFilter;
use medcenter24\mcCore\App\Services\Search\Model\Query\SearchQuery;
use medcenter24\mcCore\App\Services\Search\Model\SearchGroupBy;
use medcenter24\mcCore\App\Services\Search\Model\SearchWhere;

class SearchQueryFilterLoader extends AbstractSearchQueryLoader
{
    public function load(SearchQuery $searchQuery, Collection $collection): void
    {
        /** @var SearchFilter $filter */
        foreach ($collection as $filter) {
            $dbFilter = $this->getDbFilter($searchQuery->getFrom(), $filter);
            if (!isset($dbFilter) || !$dbFilter->loaded()) {
                continue;
            }
            foreach ($dbFilter->getJoins() as $join) {
                $searchQuery->addJoin($join);
            }
            /** @var SearchWhere $where */
            foreach ($dbFilter->getWheres() as $where) {
                $searchQuery->addWhere($where);
            }
            /** @var SearchGroupBy $group */
            foreach ($dbFilter->getGroupBy() as $group) {
                $searchQuery->addGroupBy($group);
            }
        }
    }

    private function getDbFilter(string $table, SearchFilter $filter): ?SearchDbFilter
    {
        return $this->getDbFilterFactory()->create($table, $filter);
    }

    private function getDbFilterFactory(): SearchDbFilterFactory
    {
        return $this->getServiceLocator()->get(SearchDbFilterFactory::class);
    }
}
