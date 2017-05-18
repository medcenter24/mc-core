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

    protected $fillable = ['title', 'description', 'diagnostic_category_id'];
    protected $visible = ['title', 'description', 'diagnostic_category_id'];

    public function category()
    {
        return $this->belongsTo(DiagnosticCategory::class);
    }

    public function diagnosticDoctorAccidents()
    {
        return $this->morphedByMany(DoctorAccident::class, 'diagnosticable');
    }
}
