<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccidentDoctor extends Model
{
    use SoftDeletes;

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SIGNED = 'signed';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_CLOSED = 'closed';

    protected $fillable = ['status'];

    public function docs()
    {
        $this->hasMany(Document::class);
    }

}
