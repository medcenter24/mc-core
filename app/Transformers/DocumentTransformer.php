<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Accident;
use App\DoctorAccident;
use App\Document;
use App\Patient;
use App\Services\DocumentService;
use App\Services\MediaService;
use App\User;
use League\Fractal\TransformerAbstract;

class DocumentTransformer extends TransformerAbstract
{
    /**
     * @param Document $document
     * @param MediaService $mediaService
     * @return array
     */
    public function transform (Document $document)
    {
        if ($document->accidents->count()) {
            $owner = 'accident';
        } elseif ($document->doctorAccidents->count()) {
            $owner = 'doctor';
        } elseif ($document->users->count()) {
            $owner = 'user';
        } else {
            $owner = 'patient';
        }

        $medias = $document->getMedia(DocumentService::CASES_FOLDERS);
        \Log::error('', [$medias]);
        if (!$medias->count()) {
            \Log::error('Media not found', ['document' => $document]);
            return [];
        }
        $media = $medias->first();

        $p  = $media->getPath('thumb');

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
