<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
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
    public function if(string $modelName, int $id): self
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
    public function thenValue($price = 0): self
    {
        $this->value = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Currency of the Value
     * @param $currencyId
     * @return $this
     */
    public function setCurrency($currencyId): self
    {
        $this->currencyId = $currencyId;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    /**
     * Define percent mode
     * @param string $value
     * @return $this
     */
    public function setCurrencyMode(string $value): self
    {
        $this->currencyMode = $value;
        return $this;
    }

    /**
     * Checks if the current mode is a percent mode
     * @return string
     */
    public function getCurrencyMode(): string
    {
        return $this->currencyMode;
    }

    /**
     * @return Collection
     */
    public function getCondition(): Collection
    {
        return $this->condition;
    }

    /**
     * Type of the condition - add, sub ...
     * @param string $type
     * @return $this
     */
    public function setConditionType($type = 'add'): self
    {
        $this->conditionType = $type;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getConditionType(): string
    {
        return $this->conditionType;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title = ''): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setModel(string $model = ''): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }
}
