<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Formula;


use App\Models\Formula\Exception\FormulaException;
use App\Models\Formula\FormulaBuilder;
use App\Models\Formula\FormulaBuilderInterface;
use App\Services\Formula\FormulaResultService;
use App\Services\Formula\FormulaService;
use App\Services\Formula\FormulaViewService;
use Tests\TestCase;

class FormulaTest extends TestCase
{
    /**
     * @var FormulaService
     */
    private $formulaService;

    public function setUp()
    {
        parent::setUp();
        $this->formulaService = new FormulaService(
            new FormulaViewService(),
            new FormulaResultService()
        );
    }

    /**
     * @throws \Throwable
     */
    public function testEmptyFormulaHowToUse()
    {
        $formula = $this->formulaService->formula();
        self::assertEmpty($formula->varView(), 'Empty formula produces empty view');
        self::assertEquals(0, $formula->getResult(), 'Empty formula returns 0 as a result');
    }

    /**
     * @throws \Throwable
     */
    public function testEmptyFormula()
    {
        self::assertEmpty($this->formulaService->getFormulaView($this->formulaService->formula()->getBaseFormula()), 'Empty formula produces empty view');
        self::assertEquals(0, $this->formulaService->getResult($this->formulaService->formula()->getBaseFormula()), 'Empty formula returns 0 as a result');
    }

    /**
     * @throws \Throwable
     */
    public function testAddInt()
    {
        $formula = $this->formulaService->formula()->addInteger(1);
        self::assertEquals('1', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(1, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * @throws \Throwable
     */
    public function testAddIntAddInt()
    {
        $formula = $this->formulaService->formula()->addInteger(1)->addInteger(100);
        self::assertEquals('1 + 100', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(101, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * Checking decimal
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function testAddDecimal()
    {
        $formula = $this->formulaService->formula()->addFloat(5.007, 3);
        self::assertEquals('5.007', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(5.007, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * Checking decimal + decimal
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function testAddDecimalAddDecimal()
    {
        $formula = $this->formulaService->formula()->addFloat(1)->addFloat(100);
        self::assertEquals('1.00 + 100.00', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(101.00, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');

        $formula = $this->formulaService->formula()->addFloat(15.4385)->addFloat(77.2244);
        self::assertEquals('15.44 + 77.22', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(92.66, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');

        $formula = $this->formulaService->formula()->addFloat(15.4385, 4)->addFloat(77.2244, 4);
        self::assertEquals('15.4385 + 77.2244', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(92.6629, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');

        $formula = $this->formulaService->formula()->addFloat(0.2)->addFloat(0.3);
        self::assertEquals('0.20 + 0.30', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(0.5, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * @throws \Throwable
     */
    public function testSubInt()
    {
        $formula = $this->formulaService->formula()->subInteger(1);
        self::assertEquals('1', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(1, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * @throws \Throwable
     */
    public function testSubIntSubInt()
    {
        $formula = $this->formulaService->formula()->subInteger(1)->subInteger(100);
        self::assertEquals('1 - 100', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(-99, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * @throws \Throwable
     */
    public function testSubFloatSubFloat()
    {
        $formula = $this->formulaService->formula()->subFloat(0.02)->subFloat(0.03);
        self::assertEquals('0.02 - 0.03', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(-0.01, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * int + decimal
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function testAddIntAndDecimal()
    {
        $formula = $this->formulaService->formula()->addInteger(15.4385)->addFloat(77.2244);
        self::assertEquals('15 + 77.22', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(92.22, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function testMulInt()
    {
        $formula = $this->formulaService->formula()->mulInteger(1);
        self::assertEquals('1', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(1, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function testMulIntMulInt()
    {
        $formula = $this->formulaService->formula()->mulInteger(23)->mulInteger(25);
        self::assertEquals('23 * 25', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(575, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function testMulFloatMulFloat()
    {
        $formula = $this->formulaService->formula()->mulFloat(100)->mulFloat(0.05);
        self::assertEquals('100.00 * 0.05', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(5, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');

        $formula = $this->formulaService->formula()->mulFloat(12.5501, 4)->mulFloat(22.005, 3);
        self::assertEquals('12.5501 * 22.005', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(276.1649505, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function testDivInt()
    {
        $formula = $this->formulaService->formula()->divInteger(23);
        self::assertEquals('23', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(23, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function testDivIntDivInt()
    {
        $formula = $this->formulaService->formula()->divInteger(27)->divInteger(9);
        self::assertEquals('27 / 9', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(3, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');

        $formula = $this->formulaService->formula()->divInteger(27)->divInteger(129);
        self::assertEquals('27 / 129', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(0.2093023255814, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');

        $formula = $this->formulaService->formula()->divInteger(27)->divInteger(-9);
        self::assertEquals('27 / -9', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(-3, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function testDivFloatDivFloat()
    {
        $formula = $this->formulaService->formula()->divDecimal(0.2)->divDecimal(0.3, 1);
        self::assertEquals('0.20 / 0.3', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(0.66666666666667, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     * @expectedException \App\Models\Formula\Exception\FormulaException
     * @expectedExceptionMessage Divide by zero
     */
    public function testDivideByZero()
    {
        $formula = $this->formulaService->formula()->divInteger(0);
        $this->formulaService->getFormulaView($formula->getBaseFormula());
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     * @expectedException \App\Models\Formula\Exception\FormulaException
     * @expectedExceptionMessage Divide by zero
     */
    public function testDivideByZero2()
    {
        $formula = $this->formulaService->formula()->divInteger(1)->divInteger(0);
        $this->formulaService->getFormulaView($formula->getBaseFormula());
    }

    /**
     * @throws FormulaException
     * @throws \Throwable
     */
    public function testBrackets()
    {
        $formula = $this->formulaService->formula()->addNestedFormula();
        self::assertEquals('(  )', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'View is incorrect');
        self::assertEquals(0, $this->formulaService->getResult($formula->getBaseFormula()), 'Result correct');
    }

    /**
     * Formula:
     * (2+2)*2
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function testSimpleFormula()
    {
        $formula = $this->formulaService
            ->formula()
            ->subNestedFormula()
            ->addInteger(2)
            ->addFloat(2)
            ->closeNestedFormula()
            ->mulInteger(2);

        self::assertEquals('( 2 + 2.00 ) * 2', $this->formulaService->getFormulaView($formula->getBaseFormula()), 'Empty formula produces empty view');
        self::assertEquals(8, $this->formulaService->getResult($formula->getBaseFormula()), 'Empty formula returns 0 as a result');
    }

    /**
     * Formula:
     * (2+2)*2
     * @throws \Throwable
     */
    public function testSimpleFormulaV2()
    {
        /** @var FormulaBuilderInterface $formula */
        $formula = $this->formulaService
            ->formula()
            ->subNestedFormula()
            ->addInteger(2)
            ->addFloat(2)
            ->closeNestedFormula()
            ->mulInteger(2);

        self::assertEquals('( 2 + 2.00 ) * 2', $formula->varView(), 'Empty formula produces empty view');
        self::assertEquals(8, $formula->getResult(), 'Empty formula returns 0 as a result');
    }
}
