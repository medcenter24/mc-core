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

    protected $fillable = ['title', 'price', 'type', 'created_by'];
    protected $visible = ['created_by', 'title', 'price', 'type'];

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
}
