<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// todo delete this ie i'm not storing the formulas, only the conditions
class Formula extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'commentary'];
    protected $visible = ['title', 'commentary'];
}
