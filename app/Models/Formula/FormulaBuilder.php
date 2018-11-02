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
use App\Models\Formula\Operations\Percent;
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
     * Percent which needs to be taken from this operation
     * # nested formulas can get their own percents
     * @var int
     */
    private $percent = 100;

    public function __construct(FormulaBuilderInterface $parent = null)
    {
        $this->formula = collect([]);
        $this->parent = $parent;
    }

    /**
     * @return Collection
     * @throws Exception\FormulaException
     */
    public function getFormulaCollection()
    {
        return $this->hasPercent() ? $this->attachPercent()->getFormulaCollection() : $this->formula;
    }

    /**
     * Return the Base formula
     * @return FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    public function getBaseFormula()
    {
        $base = $this;
        while ($base->hasParentFormula()) {
            $base = $base->getParentFormula();
        }
        return $base->attachPercent();
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
     * If current formula is not Base formula - top formula in the collection
     * @return bool
     */
    public function hasParentFormula()
    {
        return $this->parent !== null;
    }

    /**
     * @return bool
     */
    public function hasPercent()
    {
        return $this->percent != 100;
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
    public function divFloat($val = 1, int $precision = 2)
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
        $subFormula = new FormulaBuilder($this);
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
        $subFormula = new FormulaBuilder($this);
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
        $subFormula = new FormulaBuilder($this);
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
        $subFormula = new FormulaBuilder($this);
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

    /**
     * @param int $val
     * @return Integer
     */
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
     * Increase percents
     * @param int $percent
     * @return FormulaBuilderInterface|$this
     */
    public function addPercent($percent = 0)
    {
        $this->percent += $percent;
        return $this;
    }

    /**
     * Decrease percents
     * @param int $percent
     * @return FormulaBuilderInterface|$this
     */
    public function subPercent($percent = 0)
    {
        $this->percent -= $percent;
        return $this;
    }

    /**
     * @return int
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @return FormulaBuilderInterface
     * @throws Exception\FormulaException
     */
    private function attachPercent()
    {
        $formula = $this;
        if ($this->getPercent() !== 100) {
            $topFormula = new FormulaBuilder($this->parent);
            $topFormula->formula->push(new Mul($this));
            $this->parent = $topFormula;
            $topFormula->formula->push(new Percent($this->getInteger($this->getPercent())));
            // percent moved upper, and I need to set it to 100 to avoid recursions
            $this->percent = 100;
            $formula = $topFormula;
        }

        return $formula;
    }

    /**
     * For the compatibility and having opportunity to getting nested formulas
     * @return $this|FormulaBuilderInterface
     */
    public function varView()
    {
        return $this;
    }

    public function getResult()
    {

    }
}
