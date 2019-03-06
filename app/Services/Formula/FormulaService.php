<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services\Formula;


use App\Contract\Formula\FormulaBuilder as FormulaBuilderInterface;
use App\Exceptions\NotImplementedException;
use App\FinanceCondition;
use App\Models\Formula\FormulaBuilder;
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
     * @return FormulaBuilder
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
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    private function currencyOp(FinanceCondition $condition, FormulaBuilderInterface $builder): void
    {
        switch ($condition->type) {
            case 'sub':
                $builder->subFloat($condition->value);
                break;
            case 'add':
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