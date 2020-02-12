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

namespace medcenter24\mcCore\App\Models\Datatable;


class Relation
{

    private $table;
    private $first;
    private $operator;
    private $second;
    private $type;
    private $where;

    public function __construct(
        $table,
        $first,
        $operator = '=',
        $second = '',
        $type = '',
        $where = false
    )
    {
        $this->table = $table;
        $this->first = $first;
        $this->operator = $operator;
        $this->second = $second;
        $this->type = $type;
        $this->where = $where;
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

    /**
     * 'where' or 'on'
     * @return bool
     */
    public function getWhere(): bool
    {
        return $this->where;
    }
}
