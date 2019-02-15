<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatePeriod extends Model
{
    protected $fillable = ['title', 'from', 'to'];
    protected $visible = ['id', 'title', 'from', 'to'];

    public function interpretation()
    {
        return $this->hasMany(DatePeriodInterpretation::class);
    }
}
