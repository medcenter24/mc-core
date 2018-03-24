<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceStorage extends Model
{
    protected $table = 'finance_storage';
    protected $fillable = ['finance_condition_id', 'model', 'model_id'];
}
