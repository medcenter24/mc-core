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

namespace medcenter24\mcCore\App\Services\Search\Model\Filter\DbFilter;

use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Search\Model\Filter\SearchDbFilter;

abstract class AbstractDbFilterFactory
{
    public function create($whereValue): SearchDbFilter
    {
        return new SearchDbFilter(
            $this->isJoin(),
            $this->getJoinTable(),
            $this->getJoinFirst(),
            $this->getJoinSecond(),
            $this->getWhereField(),
            $this->getWhereOperation(),
            $this->getValues($whereValue),
            $this->andWhere(),
        );
    }

    protected function getValues(mixed $values): mixed
    {
        return $values;
    }

    abstract protected function isJoin(): bool;

    abstract protected function getJoinTable(): string;

    abstract protected function getJoinFirst(): string;

    abstract protected function getJoinSecond(): string;

    abstract protected function getWhereField(): string;

    abstract protected function getWhereOperation(): string;

    protected function andWhere(): array
    {
        return [];
    }
}
