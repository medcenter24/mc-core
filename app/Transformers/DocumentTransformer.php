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

namespace medcenter24\mcCore\App\Transformers;


use medcenter24\mcCore\App\Document;
use medcenter24\mcCore\App\Services\DocumentService;
use League\Fractal\TransformerAbstract;

class DocumentTransformer extends TransformerAbstract
{
    /**
     * @param Document $document
     * @return array
     */
    public function transform (Document $document)
    {
        if ($document->doctorAccidents->count()) {
            $owner = 'doctor';
        } elseif ($document->accidents->count()) {
            $owner = 'accident';
        } elseif ($document->users->count()) {
            $owner = 'user';
        } else {
            $owner = 'patient';
        }

        $medias = $document->getMedia(DocumentService::CASES_FOLDERS);
        if (!$medias->count()) {
            \Log::error('Media not found', ['document' => $document]);
            return [];
        }
        $media = $medias->first();
        return [
            'id' => $document->id,
            'title' => $document->title,
            'owner' => $owner,
            'fileName' => $media->file_name,
            'b64thumb' => base64_encode(file_get_contents($media->getPath('thumb'))),
            'type' => $document->type,
        ];
    }
}
