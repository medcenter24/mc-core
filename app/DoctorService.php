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
 * Services provided by a Doctor
 *
 * Class DoctorService
 * @package App
 */
class DoctorService extends Model
{
    use SoftDeletes;

    protected $fillable = ['serviceabe_id', 'serviceable_type', 'title', 'description', 'price'];
}
