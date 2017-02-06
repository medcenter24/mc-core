<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = ['user_id', 'comment'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Return Feedback model or model that was commented
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commented()
    {
        return $this->morphTo();
    }
}
