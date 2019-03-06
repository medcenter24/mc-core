<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class Doctor extends Model implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['name', 'description', 'ref_key', 'gender', 'medical_board_num'];
    protected $visible  = ['id', 'name', 'description', 'ref_key', 'gender', 'medical_board_num'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * City where doctor is (leave)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Cities which are covered by this doctor
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function cities()
    {
        return $this->morphToMany(City::class,'cityable');
    }
}
