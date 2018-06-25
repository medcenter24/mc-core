<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccidentAbstract extends Model
{
    use SoftDeletes;

    public function accident()
    {
        return $this->morphOne(Accident::class, 'caseable');
    }

    /**
     * Assignment from the Doctor_Accident to the status action with comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function statusHistory()
    {
        return $this->morphMany(AccidentStatusHistory::class, 'historyable');
    }

    public function surveys()
    {
        return [];
    }

    public function services()
    {
        return [];
    }

    public function diagnostics()
    {
        return [];
    }


}
