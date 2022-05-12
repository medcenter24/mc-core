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

namespace medcenter24\mcCore\App\Services\Search\Model\Field\DbField;

class SearchDbField
{
    public function __construct(
        private readonly string $tableName,
        private readonly string $selectField,
        private readonly string $alias,
        private readonly bool $hasOrder,
        private readonly string $order,
        private readonly array $joins,
        private readonly array $groupBy,
        private readonly array $wheres,
    ) {
    }

    public function hasOrder(): bool
    {
        return $this->hasOrder;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function getSelectField(): string
    {
        return $this->selectField;
    }

    /**
     * @return array of SearchJoin
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return array
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * @return array
     */
    public function getWheres(): array
    {
        return $this->wheres;
    }
}
