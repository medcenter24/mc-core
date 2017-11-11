<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;


use App\Helpers\DoctorTrait;
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
    use DoctorTrait;

    protected $fillable = ['title', 'description', 'diagnostic_category_id', 'disease_code', 'created_by'];
    protected $visible = ['title', 'description', 'diagnostic_category_id', 'disease_code'];

    public function category()
    {
        return $this->belongsTo(DiagnosticCategory::class);
    }

    public function diagnosticDoctorAccidents()
    {
        return $this->morphedByMany(DoctorAccident::class, 'diagnosticable');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
