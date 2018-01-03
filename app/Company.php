<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;


use App\Services\LogoService;
use App\Services\SignatureService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Spatie\MediaLibrary\Media;

class Company extends Model implements HasMedia, HasMediaConversions
{
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['title'];
    protected $visible = ['title'];

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb_250')
            ->sharpen(10)
            ->quality(80)
            ->fit(Manipulations::FIT_CROP, 250, 250)
            ->performOnCollections(LogoService::FOLDER);

        $this->addMediaConversion('thumb_300x100')
            ->sharpen(10)
            ->quality(80)
            ->fit(Manipulations::FIT_CROP, 300, 100)
            ->performOnCollections(SignatureService::FOLDER);
    }
}
