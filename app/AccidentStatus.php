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

    protected $fillable = ['title', 'type'];
    protected $visible  = ['id', 'title', 'type'];

    public function accidents()
    {
        return $this->morphedByMany(Accident::class, 'accident_statusable');
    }

    public function history()
    {
        return $this->morphTo();
    }
}
