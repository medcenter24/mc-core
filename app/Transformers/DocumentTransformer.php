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
            'preview' => $document->getFirstMedia()->id ? $document->getFirstMediaUrl() : '',
            'owner' => $owner,
            'file_name' => $document->getFirstMedia()->id ? $document->getFirstMedia()->file_name : ''
        ];
    }
}
