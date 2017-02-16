<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'address', 'phones', 'birthday', 'comment'];

    public function documents()
    {
        return $this->morphToMany(Document::class, 'documentable');
    }
}
