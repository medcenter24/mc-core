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
 * Statusable action for Accident which include commentary and time of the action
 *
 * Class AccidentStatusable
 * @package App
 */
class AccidentStatusable extends Model
{
    use SoftDeletes;

    protected $fillable = ['commentary'];

    /**
     * Type of statuses which determine where it should be used
     * (for example in Accident, Doctor_Accident or Hospital_Accident)
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function caseable()
    {
        return $this->morphTo();
    }

    public function status()
    {
        return $this->hasOne(AccidentStatus::class);
    }
}
