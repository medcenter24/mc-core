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

namespace medcenter24\mcCore\App\Contract\Formula;

use medcenter24\mcCore\App\Models\Formula\Exception\FormulaException;
use Illuminate\Support\Collection;

interface FormulaBuilder
{
    /**
     * @return Collection
     */
    public function getFormulaCollection(): Collection;

    /**
     * Get Parent formula for this
     * in case when formula is base then it will return themselves
     * @return FormulaBuilder
     */
    public function getParentFormula(): FormulaBuilder;

    /**
     * Returns top of the formula - main Collection which include all other formulas
     * @return FormulaBuilder
     */
    public function getBaseFormula(): FormulaBuilder;

    /**
     * Check if formula is nested by the some Parent formula
     * @return bool
     */
    public function hasParentFormula(): bool;

    /**
     * Adding integer
     * @param mixed $val
     * @return FormulaBuilder
     */
    public function addInteger(mixed $val = 0): FormulaBuilder;

    /**
     * Adding float
     * @param mixed $val
     * @param int $precision
     * @return FormulaBuilder
     */
    public function addFloat(mixed $val = 0, int $precision = 2): FormulaBuilder;

    /**
     * Subtract integer to the formula
     * @param mixed $val
     * @return FormulaBuilder
     */
    public function subInteger(mixed $val = 0): FormulaBuilder;

    /**
     * Subtract float
     * @param mixed $val
     * @param int $precision
     * @return FormulaBuilder
     */
    public function subFloat(mixed $val = 0, int $precision = 2): FormulaBuilder;

    /**
     * Multiply integer
     * @param mixed $val
     * @return FormulaBuilder
     */
    public function mulInteger(mixed $val = 1): FormulaBuilder;

    /**
     * Multiply float
     * @param mixed $val
     * @param int $precision
     * @return FormulaBuilder
     */
    public function mulFloat(mixed $val = 1, int $precision = 2): FormulaBuilder;

    /**
     * Divide integer
     * @param mixed $val
     * @return FormulaBuilder
     * @throws FormulaException if divide by 0
     */
    public function divInteger(mixed $val = 1): FormulaBuilder;

    /**
     * Multiply float
     * @param mixed $val
     * @param int $precision
     * @return FormulaBuilder
     * @throws FormulaException if divide by 0
     */
    public function divFloat(mixed $val = 1, int $precision = 2): FormulaBuilder;

    /**
     * Creates Nested Formula which injected with operation Add
     * @return FormulaBuilder - new sub-formula
     */
    public function addNestedFormula(): FormulaBuilder;

    /**
     * Creates Nested Formula which injected with operation Sub
     * @return FormulaBuilder - new sub-formula
     */
    public function subNestedFormula(): FormulaBuilder;

    /**
     * Creates Nested Formula which injected with operation Mul
     * @return FormulaBuilder - new sub-formula
     */
    public function mulNestedFormula(): FormulaBuilder;

    /**
     * Creates Nested Formula which injected with operation Div
     * @return FormulaBuilder - new sub-formula
     */
    public function divNestedFormula(): FormulaBuilder;

    /**
     * @return FormulaBuilder - parent formula
     */
    public function closeNestedFormula(): FormulaBuilder;

    /**
     * adding percents to the percents which needs to be taken from the result
     * @param float|int $percent
     * @return FormulaBuilder
     */
    public function addPercent(float|int $percent = 0.0): FormulaBuilder;

    /**
     * sub percents from the percents which needs to be taken from the result
     * @param float|int $percent
     * @return FormulaBuilder
     */
    public function subPercent(float|int $percent = 0.0): FormulaBuilder;

    /**
     * to define in the formula we have nested formula
     * @return FormulaBuilder
     */
    public function getVar(): FormulaBuilder;

    /**
     * If formula has conditions
     * @return bool
     */
    public function hasConditions(): bool;
}
