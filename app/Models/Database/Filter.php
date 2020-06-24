<?php

/**
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
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */
declare(strict_types=1);

namespace medcenter24\mcCore\App\Models\Database;

use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter as FilterBuilder;

class Filter
{
    private string $first;
    private string $operator;
    private string $second;
    private string $type;

    public function __construct(
        $first,
        $operator = '=',
        $second = '',
        $type = ''
    ) {
        $this->first = $first;
        $this->operator = $operator;
        $this->second = $second;
        $this->type = $type;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getFirst(): string
    {
        return $this->first;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getSecond(): string
    {
        return $this->second;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function asArray(): array
    {
        return [
            FilterBuilder::FIELD_MATCH => $this->getOperator(),
            FilterBuilder::FIELD_EL_TYPE => $this->getType(),
            FilterBuilder::FIELD_NAME => $this->getFirst(),
            FilterBuilder::FIELD_VALUE => $this->getSecond(),
        ];
    }
}
