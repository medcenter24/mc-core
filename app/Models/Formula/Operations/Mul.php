<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula\Operations;


class Mul extends AbstractOperation
{
    protected $weight = 1;

    /**
     * @return string
     */
    public function getLeftSignView()
    {
        return ' * ';
    }

    public function runOperation($result)
    {
        return $result * $this->variable->getResult();
    }
}
