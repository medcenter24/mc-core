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
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\Models\Media;

class Company extends Model implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

    const THUMB_250 = 'thumb_250';
    const THUMB_300X100 = 'thumb_300x100';

    protected $fillable = ['title', 'hospital_accident_form_id', 'doctor_accident_form_id'];
    protected $visible = ['title'];

    /**
     * @param Media|null $media
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     * todo has no sense as I become using of the Forms where all images will be placed as images with sizes
     * todo needs to be removed
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion(self::THUMB_250)
            ->sharpen(10)
            ->quality(80)
            ->fit(Manipulations::FIT_CROP, 250, 250)
            ->performOnCollections(LogoService::FOLDER);

        $this->addMediaConversion(self::THUMB_300X100)
            ->sharpen(10)
            ->quality(80)
            ->fit(Manipulations::FIT_CROP, 300, 100)
            ->performOnCollections(SignatureService::FOLDER);
    }
}
