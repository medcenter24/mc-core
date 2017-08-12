<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;



use App\Accident;
use App\Document;
use App\User;
use Illuminate\Support\Collection;

class DocumentService
{
    const DISC_IMPORTS = 'documents';
    const CASES_FOLDERS = 'patients';

    const TYPE_PASSPORT = 'passport';
    const TYPE_INSURANCE = 'insurance';

    const TYPES = [self::TYPE_PASSPORT, self::TYPE_INSURANCE];

    /**
     * @param $file
     * @param User $user
     * @param Accident $accident
     * @return Document
     */
    public function createDocumentFromFile($file, User $user, Accident $accident = null)
    {
        /** @var Document $document */
        $document = Document::create([
            'created_by' => $user->id,
            'title' => $file->getClientOriginalName()
        ]);
        $document->addMedia($file)
            ->toMediaCollection(DocumentService::CASES_FOLDERS, DocumentService::DISC_IMPORTS);

        if ($accident) {
            $accident->documents()->attach($document);
            $accident->patient->documents()->attach($document);
        } else {
            $user->documents()->attach($document);
        }

        return $document;
    }

    /**
     * @param $files
     * @param User $user
     * @param Accident|null $accident
     * @return Collection
     */
    public function createDocumentsFromFiles($files, User $user, Accident $accident = null)
    {
        $documents = new Collection();
        foreach ($files as $file) {
            $document = $this->createDocumentFromFile($file, $user, $accident);
            $documents->push($document);
        }
        return $documents;
    }

    /**
     * @param User|null $user
     * @param Accident|null $accident
     * @return Collection
     */
    public function getDocuments(User $user = null, Accident $accident = null)
    {
        $documents = new Collection();
        if ($user) {
            $documents = $documents->merge($user->documents);
        }

        if ($accident) {
            $documents = $documents->merge($accident->documents);
            if ($accident->caseable) {
                $documents = $documents->merge($accident->caseable->documents);
            }
        }

        return $documents;
    }
}
