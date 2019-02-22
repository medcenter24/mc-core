<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = ['created_by', 'value', 'currency_id', 'fixed', 'description'];
    protected $visible = ['created_by', 'value', 'currency_id', 'fixed', 'description'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'birthday'];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(FinanceCurrency::class);
    }
}
