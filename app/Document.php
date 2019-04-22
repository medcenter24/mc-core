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

use medcenter24\mcCore\App\Services\DocumentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\Models\Media;

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

    const THUMB = 'thumb';
    const PIC = 'pic';

    protected $fillable = ['title', 'created_by'];
    protected $visible = ['title', 'created_by'];

    /**
     * @param Media|null $media
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion(self::THUMB)
            ->sharpen(10)
            ->quality(50)
            ->fit(Manipulations::FIT_CROP, 368, 232)
            ->performOnCollections(DocumentService::CASES_FOLDERS);

        $this->addMediaConversion(self::PIC)
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
