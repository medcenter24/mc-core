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

namespace medcenter24\mcCore\App\Services\Search\Model\Query;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use medcenter24\mcCore\App\Services\Search\Model\SearchGroupBy;
use medcenter24\mcCore\App\Services\Search\Model\SearchJoin;
use medcenter24\mcCore\App\Services\Search\Model\SearchWhere;

class SearchQueryBuilder
{
    private array $fields = [];

    public function build(SearchQuery $searchQuery): Builder
    {
        $query = $this->createQuery($searchQuery->getFrom());
        $this->addSelect($query, $searchQuery->getFields());
        $this->addJoin($query, $searchQuery->getJoins());
        $this->addWhere($query, $searchQuery->getWheres());
        $this->addGroupBy($query, $searchQuery->getGroupBy());
        $this->addOrder($query, $searchQuery->getOrders());
        return $query;
    }

    private function createQuery(string $fromTable): Builder
    {
        return DB::table($fromTable);
    }

    private function addSelect(Builder $query, array $fields): void
    {
        if (empty($fields)) {
            $query->addSelect(DB::raw('COUNT(*) as count'));
        } else {
            $this->fields = array_merge($this->fields, $fields);
            foreach ($fields as $alias => $field) {
                $query->addSelect(DB::raw(sprintf('%s AS "%s"', $field, $alias)));
            }
        }
    }

    private function addJoin(Builder $query, array $joins): void
    {
        $joinedTables = [];
        /** @var SearchJoin $join */
        foreach ($joins as $join) {
            $joinTable = $join->getRightTable();

            if (is_array($joinTable)) {
                $joinTableName = $joinTable[0];
                $joinTableAlias = $joinTable[1];
            } else {
                $joinTableName = $joinTable;
                $joinTableAlias = $joinTable;
            }

            if (in_array($joinTableAlias, $joinedTables)) {
                continue; // do not join twice from filter and fields
            }
            $joinedTables[] = $joinTableAlias;

            $query->join(
                sprintf('%s as %s', $joinTableName, $joinTableAlias),
                static function(JoinClause $joiner) use ($join, $joinTableAlias) {
                    $joiner->on(
                        $join->getLeftTable().'.'.$join->getLeftTableField(),
                        $join->getOperator(),
                        $joinTableAlias.'.'.$join->getRightTableField(),
                    );
                },
                null,
                null,
                $join->getType(),
            );
        }
    }

    private function addGroupBy(Builder $query, array $groups): void
    {
        $grouped = [];
        /** @var SearchGroupBy $group */
        foreach ($groups as $group) {
            $key = $group->getTableName().'.'.$group->getField();

            if (in_array($key, $this->fields)) {
                $key = array_search($key, $this->fields);
            }

            if (in_array($key, $grouped)) {
                continue;
            }
            $query->groupBy($key);
            $grouped[] = $key;
        }
    }

    private function addWhere(Builder $query, array $wheres): void
    {
        /** @var SearchWhere $where */
        foreach ($wheres as $where) {
            $col = $where->getTableName().'.'.$where->getField();
            switch ($where->getOperator()) {
                case 'BETWEEN':
                    $query->whereBetween($col, $where->getValue());
                    break;
                case 'IN':
                    $query->whereIn($col, $where->getValue());
                    break;
                case '=':
                    $query->where($col, $where->getOperator(), $where->getValue());
                    break;
                case 'isNotNull':
                    if ($where->getValue()) {
                        $query->whereNotNull($col);
                    }
                    break;
                case 'isNull':
                    if ($where->getValue()) {
                        $query->whereNull($col);
                    }
                    break;
            }
        }
    }

    private function addOrder(Builder $query, array $orders): void
    {
        foreach ($orders as $field => $direction) {
            $query->orderBy($field, $direction);
        }
    }
}
