<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorSurvey extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description'];
    protected $visible = ['title', 'description'];
}
