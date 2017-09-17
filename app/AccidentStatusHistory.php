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
 * All changes which would be on accident status should include commentary
 * so we will store it in the statuses history
 *
 * Class AccidentStatusHistory
 * @package App
 */
class AccidentStatusHistory extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'accident_status_id', 'historyable_id', 'historyable_type', 'commentary'];

    public function accidentStatus()
    {
        return $this->belongsTo(AccidentStatus::class);
    }
}
