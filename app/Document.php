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
 * Document perform us files which could be used as a documents
 *  (screen shots, photos, scans, faxes ... etc)
 *
 * Class Document
 * @package App
 */
class Document extends Model implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['title'];
    protected $visible = ['title'];

    public function documentable()
    {
        return $this->morphedByMany(Patient::class, 'documentable');
    }

}
