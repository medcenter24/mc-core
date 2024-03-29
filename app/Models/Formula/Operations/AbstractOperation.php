<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Models\Formula\Operations;

use medcenter24\mcCore\App\Models\Formula\Exception\FormulaException;
use medcenter24\mcCore\App\Contract\Formula\FormulaBuilder;
use medcenter24\mcCore\App\Contract\Formula\Operation;
use medcenter24\mcCore\App\Contract\Formula\Variable;
use Throwable;

abstract class AbstractOperation implements Operation
{
    /**
     * Weight of the action (mul|div|percent needs to be done firstly)
     * @var int
     */
    protected int $weight = 0;

    protected Variable|FormulaBuilder $variable;

    /**
     * Add constructor.
     * @param $var
     * @throws FormulaException
     */
    public function __construct($var)
    {
        if ($var instanceof Variable || $var instanceof FormulaBuilder) {
            $this->variable = $var;
        } else {
            throw new FormulaException('Incorrect type of the variable for the operation');
        }
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function varView(): string
    {
        return $this->variable->varView();
    }

    /**
     * @return FormulaBuilder|Variable
     */
    public function getVar(): FormulaBuilder|Variable
    {
        return $this->variable;
    }

    /**
     * Execute operation
     * @param $result
     * @return float|int
     */
    abstract public function runOperation($result): float|int;

    /**
     * @param bool $visible
     * @return string
     */
    public function rightSignView(bool $visible = true): string
    {
        return $visible ? $this->getRightSignView() : '';
    }

    public function leftSignView(bool $visible = true): string
    {
        return $visible ? $this->getLeftSignView() : '';
    }

    protected function getLeftSignView(): string
    {
        return '';
    }

    protected function getRightSignView(): string
    {
        return '';
    }
}
