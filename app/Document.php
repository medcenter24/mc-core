<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use App\Services\DocumentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Spatie\MediaLibrary\Media;

/**
 * Document perform us files which could be used as a documents
 *  (screen shots, photos, scans, faxes ... etc)
 *
 * Class Document
 * @package App
 */
class Document extends Model implements HasMedia, HasMediaConversions
{
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['title', 'created_by'];
    protected $visible = ['title', 'created_by'];

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->sharpen(10)
            ->quality(50)
            ->fit(Manipulations::FIT_CROP, 368, 232)
            ->performOnCollections(DocumentService::CASES_FOLDERS);

        $this->addMediaConversion('pic')
            ->sharpen(10)
            ->quality(70)
            ->performOnCollections(DocumentService::CASES_FOLDERS);
    }

    public function patients()
    {
        return $this->morphedByMany(Patient::class, 'documentable');
    }

    public function doctorAccidents()
    {
        return $this->morphedByMany(DoctorAccident::class, 'documentable');
    }

    public function accidents()
    {
        return $this->morphedByMany(Accident::class, 'documentable');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'documentable');
    }
}
