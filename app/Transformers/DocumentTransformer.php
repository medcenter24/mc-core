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
use App\User;
use League\Fractal\TransformerAbstract;

class DocumentTransformer extends TransformerAbstract
{
    /**
     * @param Document $document
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

        return [
            'id' => $document->id,
            'title' => $document->title,
            'owner' => $owner,
            'fileName' => $document->hasMedia(DocumentService::CASES_FOLDERS) ?
                $document->getFirstMedia(DocumentService::CASES_FOLDERS)->file_name : '',
            'type' => $document->type,
        ];
    }
}
