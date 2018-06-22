<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceCondition extends Model
{
    use SoftDeletes;

    protected $fillable = ['created_by', 'title', 'price'];
    protected $visible = ['id', 'title', 'price'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function conditions()
    {
        return $this->hasMany(FinanceStorage::class);
    }
}
