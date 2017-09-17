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
 * Invoice can be sent to the Assistant to paid for the guarantee patient
 *
 * Class Invoice
 * @package App
 */
class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'price'];
    protected $visible = ['created_by', 'title', 'price'];

    /**
     * Invoice body stored as a FormReport element
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function formReport()
    {
        return $this->belongsTo(FormReport::class);
    }
}
