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

namespace medcenter24\mcCore\App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Document;
use medcenter24\mcCore\App\Helpers\FileHelper;
use medcenter24\mcCore\App\Services\Entity\DocumentService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DocumentTransformer extends AbstractTransformer
{
    /**
     * @param Model|Media $model
     * @return array
     */
    public function transform (Model $model): array
    {
        $media = $this->getMedia($model);
        if (!$media) {
            // document it is a media
            // without media we don't have any data
            Log::error('Media not found', ['document' => $model]);
            return [];
        }

        $fields = parent::transform($model);
        $fields['owner'] = $this->getOwner($model);
        $fields['fileName'] = $media->getAttribute('fileName');
        $thumbPath = $media->getPath('thumb');
        $fields['b64thumb'] = $thumbPath && FileHelper::isReadable($thumbPath)
            ? base64_encode(file_get_contents($thumbPath))
            : ''; // in case of wrong settings or phpunit test
        return $fields;
    }

    /**
     * @param Model $model
     * @return Media|null
     */
    private function getMedia(Model $model): ?Media
    {
        $media = null;
        if ($model instanceof Document) {
            $medias = $model->getMedia(DocumentService::CASES_FOLDERS);
            if ($medias->count()) {
                $media = $medias->first();
            }
        }
        return $media;
    }

    private function getOwner(Model $model): string
    {
        $owner = 'patient';
        if ($model->getAttribute('doctorAccidents') && $model->getAttribute('doctorAccidents')->count()) {
            $owner = 'doctor';
        } elseif ($model->getAttribute('accidents') && $model->getAttribute('accidents')->count()) {
            $owner = 'accident';
        } elseif ($model->getAttribute('users') && $model->getAttribute('users')->count()) {
            $owner = 'user';
        }
        return $owner;
    }

    protected function getMap(): array
    {
        return [
            DocumentService::FIELD_ID,
            DocumentService::FIELD_TITLE,
            DocumentService::FIELD_TYPE,
        ];
    }
}
