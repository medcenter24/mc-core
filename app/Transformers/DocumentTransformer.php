<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Document;
use App\Services\DocumentService;
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
