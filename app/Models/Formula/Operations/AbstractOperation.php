<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula\Operations;


use App\Models\Formula\Exception\FormulaException;
use App\Models\Formula\FormulaBuilderInterface;
use App\Models\Formula\Operation;
use App\Models\Formula\Variable;

abstract class AbstractOperation implements Operation
{
    /**
     * @var FormulaBuilderInterface|Variable
     */
    protected $variable;

    /**
     * Add constructor.
     * @param $var
     * @throws FormulaException
     */
    public function __construct($var)
    {
        if ($var instanceof Variable || $var instanceof FormulaBuilderInterface) {
            $this->variable = $var;
        } else {
            throw new FormulaException('Incorrect type of the variable for the operation');
        }
    }

    /**
     * @return string
     */
    public function varView()
    {
        return $this->variable->varView();
    }

    public function appendTo($result = false)
    {
        return $result === false ? $this->variable->getResult() : $this->runOperation($result);
    }

    /**
     * Execute operation
     * @param $result
     * @return mixed
     */
    abstract protected function runOperation($result);

    public function rightSignView(bool $visible = true)
    {
        return $visible ? $this->getRightSignView() : '';
    }

    public function leftSignView(bool $visible = true)
    {
        return $visible ? $this->getLeftSignView() : '';
    }

    protected function getLeftSignView() {
        return '';
    }

    protected function getRightSignView()
    {
        return '';
    }
}
