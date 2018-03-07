<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula;


use App\Models\Formula\Operations\Add;
use App\Models\Formula\Operations\Div;
use App\Models\Formula\Operations\Mul;
use App\Models\Formula\Operations\Sub;
use App\Models\Formula\Variables\Decimal;
use App\Models\Formula\Variables\Integer;
use App\Services\Formula\FormulaResultService;
use App\Services\Formula\FormulaViewService;
use Illuminate\Support\Collection;

/**
 * Generates mathematical formulas and allows to previews and execute them
 * Class Formula
 * @package App\Models\Formula
 */
class FormulaBuilder implements FormulaBuilderInterface
{
    /**
     * @var Collection
     */
    private $formula;

    /**
     * Open bracket creates new formula which will be included to the main formula
     *
     * @var null|FormulaBuilderInterface
     */
    private $parent = null;

    /**
     * We need it to print sub-formulas
     * @var FormulaViewService
     */
    private $formulaViewService;

    /**
     * to count formulas
     * @var FormulaResultService
     */
    private $formulaResultService;

    public function __construct(
        FormulaBuilderInterface $parent = null,
        FormulaViewService $formulaViewService,
        FormulaResultService $formulaResultService
    )
    {
        $this->formula = collect([]);
        $this->parent = $parent;
        $this->formulaViewService = $formulaViewService;
        $this->formulaResultService = $formulaResultService;
    }

    /**
     * @return Collection
     */
    public function getFormulaCollection()
    {
        return $this->formula;
    }

    /**
     * If current formula is not Base formula - top formula in the collection
     * @return bool
     */
    public function hasParentFormula()
    {
        return $this->parent !== null;
    }

    /**
     * Parent formula for the current formula
     * @return FormulaBuilderInterface
     */
    public function getParentFormula()
    {
        return $this->parent ?: $this;
    }

    /**
     * Return the Base formula
     * @return FormulaBuilder
     */
    public function getBaseFormula()
    {
        $base = $this;
        while ($base->hasParentFormula()) {
            $base = $base->getParentFormula();
        }
        return $base;
    }

    /**
     * @param int $val
     * @return $this|mixed
     * @throws Exception\FormulaException
     */
    public function addInteger($val = 0)
    {
        $var = $this->getInteger($val);
        $op = new Add($var);
        $this->formula->push($op);
        return $this;
    }

    /**
     * @param int $val
     * @param int $precision
     * @return $this|FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    public function addFloat($val = 0, int $precision = 2)
    {
        $var = $this->getDecimal($val, $precision);
        $op = new Add($var);
        $this->formula->push($op);
        return $this;
    }

    /**
     * @param int $val
     * @return $this|FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    public function mulInteger($val = 1)
    {
        $var = $this->getInteger($val);
        $op = new Mul($var);
        $this->formula->push($op);
        return $this;
    }

    /**
     * @param int $val
     * @param int $precision
     * @return $this|FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    public function mulFloat($val = 1, int $precision = 2)
    {
        $var = $this->getDecimal($val, $precision);
        $op = new Mul($var);
        $this->formula->push($op);
        return $this;
    }

    /**
     * @param int $val
     * @return $this|FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    public function divInteger($val = 1)
    {
        $var = $this->getInteger($val);
        $op = new Div($var);
        $this->formula->push($op);
        return $this;
    }

    /**
     * @param int $val
     * @param int $precision
     * @return $this|FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    public function divDecimal($val = 1, int $precision = 2)
    {
        $var = $this->getDecimal($val, $precision);
        $op = new Div($var);
        $this->formula->push($op);
        return $this;
    }

    /**
     * @param int $val
     * @return $this|mixed
     * @throws Exception\FormulaException
     */
    public function subInteger($val = 0)
    {
        $var = $this->getInteger($val);
        $op = new Sub($var);
        $this->formula->push($op);
        return $this;
    }

    /**
     * @param int $val
     * @param int $precision
     * @return $this|FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    public function subFloat($val = 0, int $precision = 2)
    {
        $var = $this->getDecimal($val, $precision);
        $op = new Sub($var);
        $this->formula->push($op);
        return $this;
    }

    /**
     * @return FormulaBuilder|FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    public function addNestedFormula()
    {
        $subFormula = new FormulaBuilder($this, $this->formulaViewService, $this->formulaResultService);
        $op = new Add($subFormula);
        $this->formula->push($op);
        return $subFormula;
    }

    /**
     * @return FormulaBuilder|FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    public function divNestedFormula()
    {
        $subFormula = new FormulaBuilder($this, $this->formulaViewService, $this->formulaResultService);
        $op = new Div($subFormula);
        $this->formula->push($op);
        return $subFormula;
    }

    /**
     * @return FormulaBuilder|FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    public function mulNestedFormula()
    {
        $subFormula = new FormulaBuilder($this, $this->formulaViewService, $this->formulaResultService);
        $op = new Mul($subFormula);
        $this->formula->push($op);
        return $subFormula;
    }

    /**
     * @return FormulaBuilder|FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    public function subNestedFormula()
    {
        $subFormula = new FormulaBuilder($this, $this->formulaViewService, $this->formulaResultService);
        $op = new Sub($subFormula);
        $this->formula->push($op);
        return $subFormula;
    }

    /**
     * @return FormulaBuilderInterface
     */
    public function closeNestedFormula()
    {
        return $this->getParentFormula();
    }

    private function getInteger($val = 0)
    {
        return new Integer($val);
    }

    /**
     * @param int $val
     * @param int $precision
     * @return Decimal
     */
    private function getDecimal($val = 0, int $precision = 2)
    {
        return new Decimal($val, $precision);
    }

    /**
     * @return float|int|mixed
     * @throws Exception\FormulaException
     */
    public function getResult()
    {
        return $this->formulaResultService->calculate($this);
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function varView()
    {
        $result = $this->formulaViewService->render($this);
        $result = $this->hasParentFormula() ? '( ' . $result . ' )' : $result;
        return $result;
    }
}
