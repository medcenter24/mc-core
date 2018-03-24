<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinanceCondition extends Model
{

    protected $fillable = ['created_by', 'title', 'price'];
    protected $visible = ['title', 'price'];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conditions()
    {
        return $this->belongsTo(FinanceStorage::class);
    }
}
