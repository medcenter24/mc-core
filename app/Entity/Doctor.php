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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\App\Services\Entity\UserService;
use medcenter24\mcCore\App\Services\LogoService;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Doctor extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;
    use HasFactory;

    protected $fillable = DoctorService::FILLABLE;
    protected $visible  = DoctorService::VISIBLE;

    /**
     * @param Media|null $media
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion(UserService::THUMB_45)
            ->sharpen(10)
            ->quality(50)
            ->fit(Manipulations::FIT_CROP, 45, 45)
            ->performOnCollections(LogoService::FOLDER);

        $this->addMediaConversion(UserService::THUMB_200)
            ->sharpen(10)
            ->quality(60)
            ->fit(Manipulations::FIT_CROP, 200, 200)
            ->performOnCollections(LogoService::FOLDER);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * City where doctor is (leave)
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Cities which are covered by this doctor
     * @return MorphToMany
     */
    public function cities(): MorphToMany
    {
        return $this->morphToMany(City::class,'cityable');
    }
}
