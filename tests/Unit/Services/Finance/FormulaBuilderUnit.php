<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Finance;

use App\Contract\Formula\FormulaBuilder;

/**
 * Just for one test CaseFinanceServiceTest
 * Class FormulaBuilderUnit
 * @package Tests\Unit\Services\Finance
 */
class FormulaBuilderUnit implements FormulaBuilder
{
    public function addFloat($val = 0, int $precision = 2)
    {
        return $this;
    }

    public function addInteger($val = 0)
    {
        return $this;
    }
    public function subPercent($percent = 0.0)
    {
        return $this;
    }
    public function subNestedFormula()
    {
        return $this;
    }
    public function subInteger($val = 0)
    {
        return $this;
    }
    public function subFloat($val = 0, int $precision = 2)
    {
        return $this;
    }
    public function mulNestedFormula()
    {
        return $this;
    }
    public function mulInteger($val = 1)
    {
        return $this;
    }
    public function mulFloat($val = 1, int $precision = 2)
    {
        return $this;
    }
    public function hasParentFormula()
    {
        return $this;
    }
    public function getVar()
    {
        return $this;
    }
    public function getParentFormula()
    {
        return $this;
    }
    public function getFormulaCollection()
    {
        return $this;
    }
    public function getBaseFormula()
    {
        return $this;
    }
    public function divNestedFormula()
    {
        return $this;
    }
    public function divInteger($val = 1)
    {
        return $this;
    }
    public function divFloat($val = 1, int $precision = 2)
    {
        return $this;
    }
    public function closeNestedFormula()
    {
        return $this;
    }
    public function addPercent($percent = 0.0)
    {
        return $this;
    }
    public function addNestedFormula()
    {
        return $this;
    }
}
