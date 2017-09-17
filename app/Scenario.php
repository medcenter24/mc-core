<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scenario extends Model
{
    use SoftDeletes;

    protected $fillable = ['tag', 'order', 'mode', 'accident_status_id'];

    public function accidentStatus()
    {
        return $this->belongsTo(AccidentStatus::class);
    }
}
