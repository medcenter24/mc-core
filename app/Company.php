<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24/mcCore;


use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\Services\SignatureService;
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
