<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diagnostic extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description'];

    public function doctorAccident()
    {
        return $this->belongsTo(DoctorAccident::class);
    }
}
