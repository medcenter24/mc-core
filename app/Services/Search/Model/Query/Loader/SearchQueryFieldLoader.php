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
use medcenter24\mcCore\App\Services\Search\Model\Field\DbField\SearchDbField;
use medcenter24\mcCore\App\Services\Search\Model\Field\DbField\SearchDbFieldFactory;
use medcenter24\mcCore\App\Services\Search\Model\Field\Request\SearchField;
use medcenter24\mcCore\App\Services\Search\Model\Query\SearchQuery;
use medcenter24\mcCore\App\Services\Search\Model\SearchGroupBy;

class SearchQueryFieldLoader extends AbstractSearchQueryLoader
{
    public function load(SearchQuery $searchQuery, Collection $collection): void
    {
        /** @var SearchField $field */
        foreach ($collection as $field) {
            $dbField = $this->getDbField($searchQuery->getFrom(), $field);
            if (!isset($dbField) ) {
                continue;
            }
            $searchQuery->addField([$dbField->getAlias() => $dbField->getSelectField()]);
            foreach ($dbField->getJoins() as $join) {
                $searchQuery->addJoin($join);
            }
            if ($dbField->hasOrder()) {
                $searchQuery->addOrder([$dbField->getSelectField() => $dbField->getOrder()]);
            }
            /** @var SearchGroupBy $group */
            foreach ($dbField->getGroupBy() as $group) {
                $searchQuery->addGroupBy($group);
            }
        }
    }

    private function getDbField(string $table, SearchField $field): ?SearchDbField
    {
        return $this->getDbFieldFactory()->create($table, $field);
    }

    private function getDbFieldFactory(): SearchDbFieldFactory
    {
        return $this->getServiceLocator()->get(SearchDbFieldFactory::class);
    }
}
