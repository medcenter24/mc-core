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

class DoctorSurvey extends Model
{
    use SoftDeletes;
    use DoctorTrait;

    protected $fillable = ['title', 'description', 'created_by', 'surveable_id', 'surveable_type'];
    protected $visible = ['title', 'description'];

    public function surveable()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
