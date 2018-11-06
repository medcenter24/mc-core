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

    protected $fillable = ['title', 'payment_id', 'type', 'created_by', 'status'];
    protected $visible = ['created_by', 'title', 'payment_id', 'type', 'status'];

    /**
     * File uploader
     */
    public function uploads()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    /**
     * Invoice body stored as a FormReport element
     */
    public function forms()
    {
        return $this->morphToMany(Form::class, 'formable');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
