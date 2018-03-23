<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula;


use App\Models\Formula\Exception\FormulaException;
use App\Models\Formula\Variables\Integer;
use Illuminate\Support\Collection;

interface FormulaBuilderInterface extends FormulaResultable
{
    /**
     * @return Collection
     */
    public function getFormulaCollection();

    /**
     * Check if formula is nested by the some Parent formula
     * @return bool
     */
    public function hasParentFormula();

    /**
     * Get Parent formula for this
     * in case when formula is base then it will return themselves
     * @return FormulaBuilderInterface
     */
    public function getParentFormula();

    /**
     * Returns top of the formula - main Collection which include all other formulas
     * @return FormulaBuilderInterface
     */
    public function getBaseFormula();

    /**
     * Adding integer
     * @param string|int|float $val
     * @return mixed
     */
    public function addInteger($val = 0);

    /**
     * Adding float
     * @param string|int|float $val
     * @param int $precision
     * @return FormulaBuilderInterface
     */
    public function addFloat($val = 0, int $precision = 2);

    /**
     * Subtract integer to the formula
     * @param string|int|float $val
     * @return mixed
     */
    public function subInteger($val = 0);

    /**
     * Subtract float
     * @param string|int|float $val
     * @param int $precision
     * @return FormulaBuilderInterface
     */
    public function subFloat($val = 0, int $precision = 2);

    /**
     * Multiply integer
     * @param string|int|float $val
     * @return FormulaBuilderInterface
     */
    public function mulInteger($val = 1);

    /**
     * Multiply float
     * @param string|int|float $val
     * @param int $precision
     * @return FormulaBuilderInterface
     */
    public function mulFloat($val = 1, int $precision = 2);

    /**
     * Divide integer
     * @param string|int|float $val
     * @return FormulaBuilderInterface
     * @throws FormulaException if divide by 0
     */
    public function divInteger($val = 1);

    /**
     * Multiply float
     * @param string|int|float $val
     * @param int $precision
     * @return FormulaBuilderInterface
     * @throws FormulaException if divide by 0
     */
    public function divDecimal($val = 1, int $precision = 2);

    /**
     * Creates Nested Formula which injected with operation Add
     * @return FormulaBuilderInterface - new sub-formula
     */
    public function addNestedFormula();

    /**
     * Creates Nested Formula which injected with operation Sub
     * @return FormulaBuilderInterface - new sub-formula
     */
    public function subNestedFormula();

    /**
     * Creates Nested Formula which injected with operation Mul
     * @return FormulaBuilderInterface - new sub-formula
     */
    public function mulNestedFormula();

    /**
     * Creates Nested Formula which injected with operation Div
     * @return FormulaBuilderInterface - new sub-formula
     */
    public function divNestedFormula();

    /**
     * @return FormulaBuilderInterface - parent formula
     */
    public function closeNestedFormula();

    /**
     * @return string
     * @throws \Throwable
     */
    public function varView();
}
