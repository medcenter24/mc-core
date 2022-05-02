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

namespace medcenter24\mcCore\App\Services\Search\Model\Filter\DbFilter\Factory;

use medcenter24\mcCore\App\Services\Search\Model\Filter\DbFilter\SearchDbFilter;

abstract class AbstractDbFilterFactory
{
    public function create($whereValue): SearchDbFilter
    {
        return new SearchDbFilter(
            $this->getTableName(),
            $this->getWheres($whereValue),
            $this->getJoins(),
            $this->getLoaded($whereValue),
        );
    }

    /**
     * @return string
     */
    abstract protected function getTableName(): string;

    protected function getValues(mixed $values): mixed
    {
        return $values;
    }

    protected function getJoins(): array
    {
        return [];
    }

    abstract protected function getWheres($whereValue): array;

    protected function getLoaded(mixed $whereValue): bool
    {
        return false;
    }
}
