<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Formula;


use App\Models\Formula\FormulaBuilder;
use Tests\TestCase;

class AbstractDataProvider extends TestCase
{
    /**
     * @return array
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function dataProviders()
    {
        return [
            [
                new FormulaBuilder(),
                '',
                0,
                'Empty formula'
            ],
            [
                (new FormulaBuilder())->getBaseFormula(),
                '',
                0,
                'Empty formula'
            ],
            [
                (new FormulaBuilder())->addInteger(1)->getBaseFormula(),
                '1',
                1,
                'test AddInt'
            ],
            [
                (new FormulaBuilder())->addInteger(1)->addInteger(100)->getBaseFormula(),
                '1 + 100',
                101,
                'test AddInt + AddInt'
            ],
            [
                (new FormulaBuilder())->addFloat(5.007, 3)->getBaseFormula(),
                '5.007',
                5.007,
                'test AddDecimal'
            ],
            [
                (new FormulaBuilder())->addFloat(1)->addFloat(100)->getBaseFormula(),
                '1.00 + 100.00',
                101.00,
                'test AddDecimal + Add Decimal'
            ],
            [
                (new FormulaBuilder())->addFloat(15.4385)->addFloat(77.2244)->getBaseFormula(),
                '15.44 + 77.22',
                92.66,
                'test AddDecimal + Add Decimal'
            ],
            [
                (new FormulaBuilder())->addFloat(15.4385,4)->addFloat(77.2244, 4)->getBaseFormula(),
                '15.4385 + 77.2244',
                92.6629,
                'test AddDecimal + Add Decimal'
            ],
            [
                (new FormulaBuilder())->addFloat(0.2)->addFloat(0.3)->getBaseFormula(),
                '0.20 + 0.30',
                0.5,
                'test AddDecimal + Add Decimal'
            ],
            [
                (new FormulaBuilder())->subInteger(1)->getBaseFormula(),
                '1',
                1,
                'test Sub Int'
            ],
            [
                (new FormulaBuilder())->subInteger(1)->subInteger(100)->getBaseFormula(),
                '1 - 100',
                -99,
                'test SubInt SubInt'
            ],
            [
                (new FormulaBuilder())->subFloat(0.02)->subFloat(0.03)->getBaseFormula(),
                '0.02 - 0.03',
                -0.01,
                'test SubFloat SubFloat'
            ],
            [
                (new FormulaBuilder())->addInteger(15.4385)->addFloat(77.2244)->getBaseFormula(),
                '15 + 77.22',
                92.22,
                'test AddInt AddFloat'
            ],
            [
                (new FormulaBuilder())->mulInteger(1)->getBaseFormula(),
                '1',
                1,
                'test MulInt'
            ],
            [
                (new FormulaBuilder())->mulInteger(23)->mulInteger(25)->getBaseFormula(),
                '23 * 25',
                575,
                'test MulInt MulInt'
            ],
            [
                (new FormulaBuilder())->mulFloat(100)->mulFloat(0.05)->getBaseFormula(),
                '100.00 * 0.05',
                5,
                'test MulFloat MulFloat'
            ],
            [
                (new FormulaBuilder())->mulFloat(12.5501, 4)->mulFloat(22.005, 3)->getBaseFormula(),
                '12.5501 * 22.005',
                276.1649505,
                'test MulFloat MulFloat'
            ],
            [
                (new FormulaBuilder())->divInteger(1)->getBaseFormula(),
                '1',
                1,
                'test DivInt'
            ],
            [
                (new FormulaBuilder())->divInteger(27)->divInteger(9)->getBaseFormula(),
                '27 / 9',
                3,
                'test DivInt divInt'
            ],
            [
                (new FormulaBuilder())->divInteger(27)->divInteger(129)->getBaseFormula(),
                '27 / 129',
                0.2093023255814,
                'test DivInt divInt'
            ],
            [
                (new FormulaBuilder())->divInteger(27)->divInteger(-9)->getBaseFormula(),
                '27 / -9',
                -3,
                'test DivInt divInt'
            ],
            [
                (new FormulaBuilder())->divFloat(0.2)->divFloat(0.3, 1)->getBaseFormula(),
                '0.20 / 0.3',
                0.66666666666667,
                'test divFloat divFloat'
            ],
            [
                (new FormulaBuilder())->addNestedFormula()->getBaseFormula(),
                '(  )',
                0,
                'Nested'
            ],
            [
                (new FormulaBuilder())
                    ->subNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->closeNestedFormula()
                    ->mulInteger(2)
                    ->getBaseFormula(),
                '( 2 + 2.00 ) * 2',
                8,
                'Simple formula'
            ],
            [
                (new FormulaBuilder())
                    ->addNestedFormula()
                    ->addNestedFormula()
                    ->addInteger(2)
                    ->addInteger(2)
                    ->closeNestedFormula()
                    ->mulNestedFormula()
                    ->addInteger(2)
                    ->addInteger(2)
                    ->closeNestedFormula()
                    ->closeNestedFormula()
                    ->getBaseFormula(),
                '( ( 2 + 2 ) * ( 2 + 2 ) )',
                16,
                'Complex nested'
            ],
            [
                (new FormulaBuilder())
                    ->addNestedFormula()
                    ->addNestedFormula()
                    ->addInteger(2)
                    ->addInteger(2)
                    ->mulNestedFormula()
                    ->addInteger(2)
                    ->addInteger(2)
                    ->closeNestedFormula()
                    ->closeNestedFormula()
                    ->closeNestedFormula()
                    ->getBaseFormula(),
                '( ( 2 + 2 * ( 2 + 2 ) ) )',
                10,
                'Complex nested'
            ],
            [
                (new FormulaBuilder())
                    ->subNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->closeNestedFormula()
                    ->mulInteger(2)
                    ->mulNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->closeNestedFormula()
                    ->mulInteger(2)
                    ->mulNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->mulNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->closeNestedFormula()
                    ->closeNestedFormula()
                    ->getBaseFormula(),
                '( 2 + 2.00 ) * 2 * ( 2 + 2.00 ) * 2 * ( 2 + 2.00 * ( 2 + 2.00 ) )',
                640,
                'Complex nested'
            ],
            [
                (new FormulaBuilder())
                    ->addInteger(0)
                    ->subPercent(50)
                    ->getBaseFormula(),
                '( 0 ) * 50%',
                0,
                'Percent on Zero'
            ],
            [
                (new FormulaBuilder())
                    ->addInteger(100)
                    ->subPercent(50)
                    ->getBaseFormula(),
                '( 100 ) * 50%',
                50,
                'Percent on Int'
            ],
            [
                (new FormulaBuilder())
                    ->addPercent(50)
                    ->addInteger(100)
                    ->getBaseFormula(),
                '( 100 ) * 150%',
                150,
                'Percent on Int'
            ],
            [
                (new FormulaBuilder())
                    ->addInteger(100)
                    ->addPercent(50)
                    ->getBaseFormula(),
                '( 100 ) * 150%',
                150,
                'Percent on Int'
            ],
            [
                (new FormulaBuilder())
                    ->addFloat(3.445345)
                    ->subPercent(93)
                    ->getBaseFormula(),
                '( 3.45 ) * 7%',
                0.2415,
                'Percent on Float'
            ],
            [
                (new FormulaBuilder())
                    ->subNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->closeNestedFormula()
                    ->mulInteger(2)
                    ->mulNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->closeNestedFormula()
                    ->mulInteger(2)
                    ->mulNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->mulNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->closeNestedFormula()
                    ->closeNestedFormula()
                    ->subPercent(50)
                    ->getBaseFormula(),
                '( ( 2 + 2.00 ) * 2 * ( 2 + 2.00 ) * 2 * ( 2 + 2.00 * ( 2 + 2.00 ) ) ) * 50%',
                320,
                'Complex nested with percent'
            ],
            [
                (new FormulaBuilder())
                    ->subNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->subPercent(50)
                    ->closeNestedFormula()
                    ->mulInteger(2)
                    ->mulNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->closeNestedFormula()
                    ->mulInteger(2)
                    ->mulNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->mulNestedFormula()
                    ->addInteger(2)
                    ->addFloat(2)
                    ->closeNestedFormula()
                    ->closeNestedFormula()
                    ->subPercent(50)
                    ->getBaseFormula(),
                '( ( ( 2 + 2.00 ) * 50% ) * 2 * ( 2 + 2.00 ) * 2 * ( 2 + 2.00 * ( 2 + 2.00 ) ) ) * 50%',
                160,
                'Complex nested with percent'
            ]
        ];
    }
}