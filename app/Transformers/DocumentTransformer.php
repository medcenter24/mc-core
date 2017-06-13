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
        $owner = '';

        switch ($document->documentable()->documentable_type)
        {
            case Patient::class:
                $owner = 'patient';
                break;
            case DoctorAccident::class:
                $owner = 'doctor';
                break;
            case Accident::class:
                $owner = 'accident';
                break;
            case User::class:
                $owner = 'user';
                break;
        }

        return [
            'id' => $document->id,
            'title' => $document->title,
            'preview' => $document->getFirstMediaUrl(),
            'owner' => $owner,
            'file_name' => $document->getFirstMedia()->file_name
        ];
    }
}
