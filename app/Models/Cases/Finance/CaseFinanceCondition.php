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

/**
 * Class CaseFinanceCondition
 * @package App\Models\Cases\Finance
 */
class CaseFinanceCondition
{
    /**
     * @var Collection
     */
    private $condition;

    /**
     * @var int
     */
    private $value = 0;

    /**
     * Currency of the condition
     * @var int
     */
    private $currencyId = 0;

    /**
     * Mode of the currency:
     *  - Currency - to use with table currencies
     *  - Percent - if the value is a percent to calculate from the total
     * @var string
     */
    private $currencyMode = '';

    /**
     * Type of the condition:
     *      determines how to apply the value to the final formula
     *
     * Possible values:
     *      add "+"
     *      subtract "-"
     * @var string
     */
    private $conditionType = 'add';

    /**
     * @var string
     */
    private $title = '';

    /**
     * Model of the condition to know for what we need to attach this condition
     * For example
     *  Doctor::class - to calculate income for the doctor from this case
     *  Accident::class - to calculate income for the company from the case
     * @var string
     */
    private $model;

    /**
     * CaseFinanceCondition constructor.
     */
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
    public function thenValue($price = 0)
    {
        $this->value = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Currency of the Value
     * @param $currencyId
     * @return $this
     */
    public function setCurrency($currencyId)
    {
        $this->currencyId = $currencyId;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * Define percent mode
     * @param bool $value
     * @return $this
     */
    public function setCurrencyMode($value = true)
    {
        $this->currencyMode = $value;
        return $this;
    }

    /**
     * Checks if the current mode is a percent mode
     * @return bool
     */
    public function getCurrencyMode()
    {
        return $this->currencyMode;
    }

    /**
     * @return Collection
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Type of the condition - add, sub ...
     * @param string $type
     * @return $this
     */
    public function setConditionType($type = 'add')
    {
        $this->conditionType = $type;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getConditionType()
    {
        return $this->conditionType;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title = '')
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setModel(string $model = '')
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }
}
