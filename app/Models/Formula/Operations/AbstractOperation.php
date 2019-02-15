<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula\Operations;


use App\Models\Formula\Exception\FormulaException;
use App\Contract\Formula\FormulaBuilder;
use App\Contract\Formula\Operation;
use App\Contract\Formula\Variable;

abstract class AbstractOperation implements Operation
{
    /**
     * Weight of the action (mul|div|percent needs to be done firstly)
     * @var int
     */
    protected $weight = 0;

    /**
     * @var FormulaBuilder|Variable
     */
    protected $variable;

    /**
     * Add constructor.
     * @param $var
     * @throws FormulaException
     */
    public function __construct($var)
    {
        if ($var instanceof Variable || $var instanceof FormulaBuilder) {
            $this->variable = $var;
        } else {
            throw new FormulaException('Incorrect type of the variable for the operation');
        }
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function varView(): string
    {
        return $this->variable->varView();
    }

    /**
     * @return FormulaBuilder|Variable
     */
    public function getVar()
    {
        return $this->variable;
    }

    /**
     * Execute operation
     * @param $result
     * @return mixed
     */
    abstract public function runOperation($result);

    /**
     * @param bool $visible
     * @return string
     */
    public function rightSignView(bool $visible = true): string
    {
        return $visible ? $this->getRightSignView() : '';
    }

    public function leftSignView(bool $visible = true): string
    {
        return $visible ? $this->getLeftSignView() : '';
    }

    protected function getLeftSignView(): string
    {
        return '';
    }

    protected function getRightSignView(): string
    {
        return '';
    }
}
