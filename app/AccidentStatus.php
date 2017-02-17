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
 * Statuses of the Accident
 *
 * Class AccidentStatus
 * @package App
 */
class AccidentStatus extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description'];

    public function statusable()
    {
        return $this->morphTo();
    }
}
