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

namespace medcenter24\mcCore\Tests\Unit\Services\Finance;

use medcenter24\mcCore\App\Contract\Formula\FormulaBuilder;
use Illuminate\Support\Collection;

/**
 * Just for one test CaseFinanceServiceTest
 * Class FormulaBuilderUnit
 * @package Tests\Unit\Services\Finance
 */
class FormulaBuilderUnit implements FormulaBuilder
{
    public function addFloat($val = 0, int $precision = 2): FormulaBuilder
    {
        return $this;
    }

    public function addInteger($val = 0): FormulaBuilder
    {
        return $this;
    }
    public function subPercent($percent = 0.0): FormulaBuilder
    {
        return $this;
    }
    public function subNestedFormula(): FormulaBuilder
    {
        return $this;
    }
    public function subInteger($val = 0): FormulaBuilder
    {
        return $this;
    }
    public function subFloat($val = 0, int $precision = 2): FormulaBuilder
    {
        return $this;
    }
    public function mulNestedFormula(): FormulaBuilder
    {
        return $this;
    }
    public function mulInteger($val = 1): FormulaBuilder
    {
        return $this;
    }
    public function mulFloat($val = 1, int $precision = 2): FormulaBuilder
    {
        return $this;
    }
    public function hasParentFormula(): bool
    {
        return $this;
    }
    public function getVar(): FormulaBuilder
    {
        return $this;
    }
    public function getParentFormula(): FormulaBuilder
    {
        return $this;
    }
    public function getFormulaCollection(): Collection
    {
        return $this;
    }
    public function getBaseFormula(): FormulaBuilder
    {
        return $this;
    }
    public function divNestedFormula(): FormulaBuilder
    {
        return $this;
    }
    public function divInteger($val = 1): FormulaBuilder
    {
        return $this;
    }
    public function divFloat($val = 1, int $precision = 2): FormulaBuilder
    {
        return $this;
    }
    public function closeNestedFormula(): FormulaBuilder
    {
        return $this;
    }
    public function addPercent($percent = 0.0): FormulaBuilder
    {
        return $this;
    }
    public function addNestedFormula(): FormulaBuilder
    {
        return $this;
    }
    public function hasConditions(): bool
    {
        return true;
    }
}
