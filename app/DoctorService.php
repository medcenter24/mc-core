<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorService extends Model
{
    use SoftDeletes;

    protected $fillable = ['serviciabe_id', 'serviciable_type', 'title', 'description', 'price'];
}
