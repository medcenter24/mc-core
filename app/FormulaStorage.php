<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// todo delete this with Formula.php and all storages for that
class FormulaStorage extends Model
{
    use SoftDeletes;

    protected $table = 'formula_storage';
    protected $fillable = ['formula_id', 'operation_class', 'variable_class', 'variable_id'];
}
