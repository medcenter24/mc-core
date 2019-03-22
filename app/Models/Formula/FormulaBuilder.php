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

namespace App\Models\Formula;


use App\Contract\Formula\FormulaBuilder as FormulaBuilderContract;
use App\Models\Formula\Operations\Add;
use App\Models\Formula\Operations\Div;
use App\Models\Formula\Operations\Mul;
use App\Models\Formula\Operations\Percent;
use App\Models\Formula\Operations\Sub;
use App\Models\Formula\Variables\Decimal;
use App\Models\Formula\Variables\Integer as VarInt;
use Illuminate\Support\Collection;

/**
 * Generates mathematical formulas and allows to previews and execute them
 * Class Formula
 * @package App\Models\Formula
 */
class FormulaBuilder implements FormulaBuilderContract
{
    /**
     * @var Collection
     */
    private $formula;

    /**
     * Open bracket creates new formula which will be included to the main formula
     *
     * @var null|FormulaBuilderContract
     */
    private $parent;

    /**
     * Percent which needs to be taken from this operation
     * # nested formulas can get their own percents
     * @var int
     */
    private $percent = 100;

    public function __construct(FormulaBuilderContract $parent = null)
    {
        $this->formula = collect([]);
        $this->parent = $parent;
    }

    /**
     * @return Collection
     * @throws Exception\FormulaException
     */
    public function getFormulaCollection(): Collection
    {
        return $this->hasPercent() ? $this->attachPercent()->getFormulaCollection() : $this->formula;
    }

    /**
     * Return the Base formula
     * @return FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    public function getBaseFormula(): FormulaBuilderContract
    {
        $base = $this;
        while ($base->hasParentFormula()) {
            $base = $base->getParentFormula();
        }

        return $base->attachPercent();
    }

    /**
     * Parent formula for the current formula
     * @return FormulaBuilderContract
     */
    public function getParentFormula(): FormulaBuilderContract
    {
        return $this->parent ?: $this;
    }

    /**
     * If current formula is not Base formula - top formula in the collection
     * @return bool
     */
    public function hasParentFormula(): bool
    {
        return $this->parent !== null;
    }

    /**
     * @return bool
     */
    public function hasPercent(): bool
    {
        return $this->percent !== 100;
    }

    /**
     * @param int $val
     * @return $this
     * @throws Exception\FormulaException
     */
    public function addInteger($val = 0): FormulaBuilderContract
    {
        $var = $this->getInteger($val);
        $op = new Add($var);
        $this->formula->push($op);

        return $this;
    }

    /**
     * @param int $val
     * @param int $precision
     * @return $this|FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    public function addFloat($val = 0, int $precision = 2): FormulaBuilderContract
    {
        $var = $this->getDecimal($val, $precision);
        $op = new Add($var);
        $this->formula->push($op);

        return $this;
    }

    /**
     * @param int $val
     * @return $this|FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    public function mulInteger($val = 1): FormulaBuilderContract
    {
        $var = $this->getInteger($val);
        $op = new Mul($var);
        $this->formula->push($op);

        return $this;
    }

    /**
     * @param int $val
     * @param int $precision
     * @return $this|FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    public function mulFloat($val = 1, int $precision = 2):FormulaBuilderContract
    {
        $var = $this->getDecimal($val, $precision);
        $op = new Mul($var);
        $this->formula->push($op);

        return $this;
    }

    /**
     * @param int $val
     * @return $this|FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    public function divInteger($val = 1): FormulaBuilderContract
    {
        $var = $this->getInteger($val);
        $op = new Div($var);
        $this->formula->push($op);

        return $this;
    }

    /**
     * @param int $val
     * @param int $precision
     * @return $this|FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    public function divFloat($val = 1, int $precision = 2): FormulaBuilderContract
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
    public function subInteger($val = 0): FormulaBuilderContract
    {
        $var = $this->getInteger($val);
        $op = new Sub($var);
        $this->formula->push($op);

        return $this;
    }

    /**
     * @param int $val
     * @param int $precision
     * @return $this|FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    public function subFloat($val = 0, int $precision = 2): FormulaBuilderContract
    {
        $var = $this->getDecimal($val, $precision);
        $op = new Sub($var);
        $this->formula->push($op);

        return $this;
    }

    /**
     * @return FormulaBuilder|FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    public function addNestedFormula(): FormulaBuilderContract
    {
        $subFormula = new FormulaBuilder($this);
        $op = new Add($subFormula);
        $this->formula->push($op);

        return $subFormula;
    }

    /**
     * @return FormulaBuilder|FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    public function divNestedFormula(): FormulaBuilderContract
    {
        $subFormula = new FormulaBuilder($this);
        $op = new Div($subFormula);
        $this->formula->push($op);

        return $subFormula;
    }

    /**
     * @return FormulaBuilder|FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    public function mulNestedFormula(): FormulaBuilderContract
    {
        $subFormula = new FormulaBuilder($this);
        $op = new Mul($subFormula);
        $this->formula->push($op);

        return $subFormula;
    }

    /**
     * @return FormulaBuilder|FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    public function subNestedFormula(): FormulaBuilderContract
    {
        $subFormula = new FormulaBuilder($this);
        $op = new Sub($subFormula);
        $this->formula->push($op);

        return $subFormula;
    }

    /**
     * @return FormulaBuilderContract
     */
    public function closeNestedFormula(): FormulaBuilderContract
    {
        return $this->getParentFormula();
    }

    /**
     * @param int $val
     * @return VarInt|FormulaBuilderContract
     */
    private function getInteger($val = 0)
    {
        return $val instanceof FormulaBuilderContract ? $val : new VarInt($val);
    }

    /**
     * @param int $val
     * @param int $precision
     * @return Decimal|FormulaBuilderContract
     */
    private function getDecimal($val = 0, int $precision = 2)
    {
        return $val instanceof FormulaBuilderContract ? $val : new Decimal($val, $precision);
    }

    /**
     * Increase percents
     * @param int $percent
     * @return FormulaBuilderContract|$this
     */
    public function addPercent($percent = 0): FormulaBuilderContract
    {
        $this->percent += $percent;

        return $this;
    }

    /**
     * Decrease percents
     * @param int $percent
     * @return FormulaBuilderContract|$this
     */
    public function subPercent($percent = 0): FormulaBuilderContract
    {
        $this->percent -= $percent;

        return $this;
    }

    /**
     * @return int
     */
    public function getPercent(): int
    {
        return $this->percent;
    }

    /**
     * @return FormulaBuilderContract
     * @throws Exception\FormulaException
     */
    private function attachPercent(): FormulaBuilderContract
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
     * @return $this|FormulaBuilderContract
     */
    public function getVar(): FormulaBuilderContract
    {
        return $this;
    }

    /**
     * @return bool
     */
    public function hasConditions(): bool
    {
        return (bool) $this->formula->count();
    }
}
