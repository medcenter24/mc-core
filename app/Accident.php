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
 * Case|Accident|...
 *
 * Class Accident
 * @package App
 */
class Accident extends Model
{
    use SoftDeletes;

    protected $fillable = [];

    /**
     * Statuses which related to accident model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function accidentStatuses()
    {
        return $this->morphMany(AccidentStatus::class, 'caseable');
    }

    /**
     * Statuses history with commentaries
     * if we use that relation we can restore history about changing statuses
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function accidentStatusables()
    {
        return $this->morphMany(AccidentStatusable::class, 'statusable');
    }

    /**
     * Statuses which can be used for Accident model
     * after director will configure statuses, accident cases can use it
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accidentStatus()
    {
        return $this->belongsTo(AccidentStatusable::class);
    }
}
