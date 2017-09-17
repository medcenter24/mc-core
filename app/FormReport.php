<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Reports which generated for the Accident
 *
 * Class AccidentReportForm
 * @package App
 */
class FormReport extends Model
{
    use SoftDeletes;

    protected $fillable = ['values'];
    protected $visible = ['values'];

    protected function forms()
    {
        return $this->belongsTo(Form::class);
    }
}
