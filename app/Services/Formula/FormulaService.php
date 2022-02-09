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

namespace medcenter24\mcCore\App\Services\Formula;

use medcenter24\mcCore\App\Contract\Formula\FormulaBuilder as FormulaBuilderInterface;
use medcenter24\mcCore\App\Exceptions\NotImplementedException;
use medcenter24\mcCore\App\Entity\FinanceCondition;
use medcenter24\mcCore\App\Models\Formula\Exception\FormulaException;
use medcenter24\mcCore\App\Models\Formula\FormulaBuilder;
use Illuminate\Support\Collection;

class FormulaService
{
    /**
     * @param FormulaBuilderInterface|null $parent
     * @return FormulaBuilder
     */
    public function createFormula(FormulaBuilderInterface $parent = null): FormulaBuilderInterface
    {
        return new FormulaBuilder($parent);
    }

    /**
     * @param Collection $conditions
     * @return FormulaBuilderInterface
     * @throws NotImplementedException
     * @throws FormulaException
     */
    public function createFormulaFromConditions(Collection $conditions): FormulaBuilderInterface
    {
        $builder = $this->createFormula();
        $conditions->each(function(FinanceCondition $condition) use ($builder) {
            switch ($condition->currency_mode) {
                case 'currency':
                    $this->currencyOp($condition, $builder);
                    break;
                case 'percent':
                    $this->percentOp($condition, $builder);
                    break;
                default: throw new NotImplementedException('Undefined currency mode');
            }
        });
        return $builder;
    }

    /**
     * Operations with currencies
     * @param FinanceCondition $condition
     * @param FormulaBuilderInterface $builder
     * @throws NotImplementedException
     * @throws FormulaException
     */
    private function currencyOp(FinanceCondition $condition, FormulaBuilderInterface $builder): void
    {
        switch ($condition->type) {
            case 'sub':
                $builder->subFloat($condition->value);
                break;
            case 'add':
            case 'base':
                $builder->addFloat($condition->value);
                break;
            case 'mul':
                $builder->mulFloat($condition->value);
                break;
            case 'div':
                $builder->divFloat($condition->value);
                break;
            default: throw new NotImplementedException('Undefined operation type');
        }
    }

    /**
     * Operations with percents
     * @param FinanceCondition $condition
     * @param FormulaBuilderInterface $builder
     * @throws NotImplementedException
     */
    private function percentOp(FinanceCondition $condition, FormulaBuilderInterface $builder): void
    {
        switch ($condition->type) {
            case 'sub':
                $builder->subPercent($condition->value);
                break;
            case 'add':
                $builder->addPercent($condition->value);
                break;
            default: throw new NotImplementedException('Undefined percent operation');
        }
    }
}