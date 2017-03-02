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
 * Kinds of the diagnostics that should be done by the Doctor through the Accident
 *
 * Class Diagnostic
 * @package App
 */
class Diagnostic extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description'];
    protected $visible = ['title', 'description'];

    public function doctorAccident()
    {
        return $this->belongsTo(DoctorAccident::class);
    }

    public function category()
    {
        return $this->hasOne(DiagnosticCategory::class);
    }
}
