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
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use medcenter24\mcCore\App\Helpers\MediaHelper;
use medcenter24\mcCore\App\Services\Entity\DocumentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
    use InteractsWithMedia;
    use HasFactory;

    protected $fillable = DocumentService::FILLABLE;
    protected $visible = DocumentService::VISIBLE;

    /**
     * @param Media|null $media
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion(DocumentService::THUMB)
            ->sharpen(10)
            ->quality(50)
            ->fit(Manipulations::FIT_CROP, 368, 232)
            ->performOnCollections(DocumentService::CASES_FOLDERS);

        $this->addMediaConversion(DocumentService::PIC)
            ->sharpen(10)
            ->quality(70)
            ->performOnCollections(DocumentService::CASES_FOLDERS);
    }

    public function patients(): MorphToMany
    {
        return $this->morphedByMany(Patient::class, 'documentable');
    }

    public function doctorAccidents(): MorphToMany
    {
        return $this->morphedByMany(DoctorAccident::class, 'documentable');
    }

    public function accidents(): MorphToMany
    {
        return $this->morphedByMany(Accident::class, 'documentable');
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'documentable');
    }

    public function b64(): string {
        return MediaHelper::b64($this, DocumentService::CASES_FOLDERS, DocumentService::PIC);
    }

    public function getAttribute($key)
    {
        if ($key === 'b64') {
            $val = $this->b64();
        } else {
            $val = parent::getAttribute($key);
        }

        return $val;
    }
}
