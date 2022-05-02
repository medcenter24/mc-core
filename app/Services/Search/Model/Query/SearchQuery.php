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

use medcenter24\mcCore\App\Services\Search\Model\SearchJoin;
use medcenter24\mcCore\App\Services\Search\Model\SearchWhere;

class SearchQuery
{
    public function __construct(
        private string $from   = '', // from table
        private array $fields  = [], // for select ['table.field']
        private array $joins   = [], // [SearchJoin]
        private array $orders  = [], // [table.field => 'asc/desc']
        private array $wheres  = [], // [table.field, operation, value]
    ) {
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @param array $field [alias => table.fieldName]
     * @return void
     */
    public function addField(array $field): void
    {
        $this->fields = array_merge($this->fields, $field);
    }

    /**
     * @return array
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @param array $joins
     */
    public function setJoins(array $joins): void
    {
        $this->joins = $joins;
    }

    /**
     * @return array
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * @param array $orders
     */
    public function setOrders(array $orders): void
    {
        $this->orders = $orders;
    }

    /**
     * @return array
     */
    public function getWheres(): array
    {
        return $this->wheres;
    }

    /**
     * @param array $wheres
     */
    public function setWheres(array $wheres): void
    {
        $this->wheres = $wheres;
    }

    public function addWhere(SearchWhere $where): void
    {
        $this->wheres[] = $where;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom(string $from): void
    {
        $this->from = $from;
    }

    public function addJoin(SearchJoin $join): void
    {
        $this->joins[] = $join;
    }

    public function addOrder(array $order): void
    {
        $this->orders = array_merge($this->orders, $order);
    }
}
