<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Contract\Formula;


use App\Models\Formula\Exception\FormulaException;
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
     * @param string|int|float $val
     * @return FormulaBuilder
     */
    public function addInteger($val = 0): FormulaBuilder;

    /**
     * Adding float
     * @param string|int|float $val
     * @param int $precision
     * @return FormulaBuilder
     */
    public function addFloat($val = 0, int $precision = 2): FormulaBuilder;

    /**
     * Subtract integer to the formula
     * @param string|int|float $val
     * @return FormulaBuilder
     */
    public function subInteger($val = 0): FormulaBuilder;

    /**
     * Subtract float
     * @param string|int|float $val
     * @param int $precision
     * @return FormulaBuilder
     */
    public function subFloat($val = 0, int $precision = 2): FormulaBuilder;

    /**
     * Multiply integer
     * @param string|int|float $val
     * @return FormulaBuilder
     */
    public function mulInteger($val = 1): FormulaBuilder;

    /**
     * Multiply float
     * @param string|int|float $val
     * @param int $precision
     * @return FormulaBuilder
     */
    public function mulFloat($val = 1, int $precision = 2): FormulaBuilder;

    /**
     * Divide integer
     * @param string|int|float $val
     * @return FormulaBuilder
     * @throws FormulaException if divide by 0
     */
    public function divInteger($val = 1): FormulaBuilder;

    /**
     * Multiply float
     * @param string|int|float $val
     * @param int $precision
     * @return FormulaBuilder
     * @throws FormulaException if divide by 0
     */
    public function divFloat($val = 1, int $precision = 2): FormulaBuilder;

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
     * @param float $percent
     * @return FormulaBuilder
     */
    public function addPercent($percent = 0.0): FormulaBuilder;

    /**
     * sub percents from the percents which needs to be taken from the result
     * @param float $percent
     * @return FormulaBuilder
     */
    public function subPercent($percent = 0.0): FormulaBuilder;

    /**
     * to define in in the formula we have nested formula
     * @return FormulaBuilder
     */
    public function getVar(): FormulaBuilder;

    /**
     * If formula has conditions
     * @return bool
     */
    public function hasConditions(): bool;
}