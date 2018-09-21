<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceCurrency extends Model
{
    use SoftDeletes;

    protected $table = 'finance_currencies';
    protected $fillable = ['title', 'code', 'ico'];
    protected $visible = ['id', 'title', 'code', 'ico'];
}
