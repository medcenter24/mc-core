<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = ['user_id', 'token', 'valid_from', 'valid_to'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
