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

namespace medcenter24\mcCore\App\Services\Search\Model\Field;

class SearchDbField
{
    /**
     * @param bool $hasJoin
     * @param string $joinTable
     * @param string $joinFirst
     * @param string $joinSecond
     * @param string $selectField
     * @param bool $hasOrder
     * @param string $order
     */
    public function __construct(
        private readonly bool $hasJoin,
        private readonly string $joinTable,
        private readonly string $joinFirst,
        private readonly string $joinSecond,
        private readonly string $selectField,
        private readonly bool $hasOrder,
        private readonly string $order,
    ) {
    }

    public function hasJoin(): bool
    {
        return $this->hasJoin;
    }

    public function getJoinTable(): string
    {
        return $this->joinTable;
    }

    public function getJoinFirst(): string
    {
        return $this->joinFirst;
    }

    public function getSelectField(): string
    {
        return $this->selectField;
    }

    public function hasOrder(): bool
    {
        return $this->hasOrder;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getJoinSecond(): string
    {
        return $this->joinSecond;
    }
}
