<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Finance;

use App\Contract\Formula\FormulaBuilder;
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
}
