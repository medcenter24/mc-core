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

namespace medcenter24\mcCore\App\Services\Search\Model;

class SearchJoin
{
    public function __construct(
        private string $leftTable = '',
        private string|array $rightTable = '', // array if alias
        private string $leftTableField = '',
        private string $rightTableField = '',
        private string $operator = '=',
        private string $type = 'left',
    ) { }

    /**
     * @return string
     */
    public function getLeftTable(): string
    {
        return $this->leftTable;
    }

    /**
     * @param string $leftTable
     */
    public function setLeftTable(string $leftTable): void
    {
        $this->leftTable = $leftTable;
    }

    /**
     * @return string|array
     */
    public function getRightTable(): string|array
    {
        return $this->rightTable;
    }

    /**
     * @param string|array $rightTable
     */
    public function setRightTable(string|array $rightTable): void
    {
        $this->rightTable = $rightTable;
    }

    /**
     * @return string
     */
    public function getLeftTableField(): string
    {
        return $this->leftTableField;
    }

    /**
     * @param string $leftTableField
     */
    public function setLeftTableField(string $leftTableField): void
    {
        $this->leftTableField = $leftTableField;
    }

    /**
     * @return string
     */
    public function getRightTableField(): string
    {
        return $this->rightTableField;
    }

    /**
     * @param string $rightTableField
     */
    public function setRightTableField(string $rightTableField): void
    {
        $this->rightTableField = $rightTableField;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     */
    public function setOperator(string $operator): void
    {
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
