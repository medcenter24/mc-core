<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula\Operations;


use App\Contract\Formula\Variable;
use App\Models\Formula\Exception\FormulaException;

class Div extends AbstractOperation
{
    protected $weight = 1;

    /**
     * Div constructor.
     * @param Variable $var
     * @throws FormulaException
     */
    public function __construct(Variable $var)
    {
        if ($var->getResult() === 0) {
            throw new FormulaException('Divide by zero');
        }
        parent::__construct($var);
    }

    /**
     * @return string
     */
    public function getLeftSignView(): string
    {
        return ' / ';
    }

    public function runOperation($result)
    {
        return $result / $this->variable->getResult();
    }
}
