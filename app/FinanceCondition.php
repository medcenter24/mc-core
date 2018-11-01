<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model
 *   model property used for the determination which calculation do we need to use: for the doctor or for the accident (maybe something else will be in this list)
 *   So If is set Doctor then this condition will be used only for calculation refund of the doctors
 *      If is set to Accident then this condition will be used for income of the Accident
 *   TODO Make sure that doctors' refund should not be bigger than accidents' income, otherwise it is waste money
 *
 * Currency
 *     currency - can be real money (euro, dollars, rubles which has not been implemented yet) or percent
 *     In case with `money` we just adding them to the formula
 *     In case with `percent` we need to apply this percents to all formulas percent
 *
 * Type
 *     property which allows us to know what do we need to do with this value (amount) - add or sub them from the general formula
 *      Add +
 *      Sub -
 *
 * CurrencyMode
 *      'percent' - calculate percents of the total
 *      'currency' - using currency_id value to add or dec value from the total
 *
 *
 * Class FinanceCondition
 * @package App
 */
class FinanceCondition extends Model
{
    use SoftDeletes;

    protected $fillable = ['created_by', 'title', 'value', 'type', 'currency_id', 'currency_mode', 'model'];
    protected $visible = ['id', 'title', 'value', 'type', 'currency_id', 'currency_mode', 'model'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function conditions()
    {
        return $this->hasMany(FinanceStorage::class);
    }
}
