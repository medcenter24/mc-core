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
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

/**
 * Insurance partners
 *
 * Class Assistant
 * @package App
 */
class Assistant extends Model implements HasMedia
{

    use SoftDeletes;
    // logo
    use HasMediaTrait;

    protected $fillable = ['title', 'ref_key', 'email', 'comment'];
    protected $visible = ['id', 'title', 'ref_key', 'email', 'comment'];
}
