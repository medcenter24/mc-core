<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class DatePeriodInterpretation extends Model
{
    protected $fillable = ['date_period_id', 'day_of_week', 'from', 'to'];
    protected $visible = ['date_period_id', 'day_of_week', 'from', 'to'];
}
