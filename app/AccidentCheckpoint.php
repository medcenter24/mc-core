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
 * A lot of checkpoints should be done through Accident
 * so let keep list of them to cross finished steps
 *
 * Class AccidentCheckpoint
 * @package App
 */
class AccidentCheckpoint extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description'];
    protected $visible = ['title', 'description'];

    protected function accidents()
    {
        return $this->belongsTo(Accident::class);
    }
}
