<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Cases\Finance;


use App\Models\Cases\Finance\Operations\IfOperation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CaseFinanceCondition
{
    /**
     * @var Collection
     */
    private $condition;

    /**
     * @var int
     */
    private $price = 0;

    public function __construct()
    {
        $this->condition = collect([]);
    }

    /**
     * @param Model|string $modelName
     * @param int $id
     * @return $this
     */
    public function if(string $modelName, int $id)
    {
        $op = new IfOperation($modelName, $id);
        $this->condition->push($op);
        return $this;
    }

    /**
     * Set price for the rule
     * @param int $price
     * @return $this
     */
    public function thenPrice($price = 0)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }
}
