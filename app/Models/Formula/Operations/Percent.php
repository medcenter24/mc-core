<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula\Operations;


class Percent extends AbstractOperation
{
    /**
     * @return string
     */
    public function getLeftSignView()
    {
        return '*';
    }

    public function getRightSignView()
    {
        return '%';
    }

    public function runOperation($result)
    {
        return $result * ($this->variable->getResult() / 100);
    }
}
