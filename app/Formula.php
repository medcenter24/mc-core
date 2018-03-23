<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formula extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'commentary'];
    protected $visible = ['title', 'commentary'];
}
