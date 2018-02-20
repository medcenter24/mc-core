<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TelegramUser extends Model
{
    use SoftDeletes;

    protected $fillable = ['telegram_id', 'user_id', 'username', 'last_visit', 'first_name', 'last_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
