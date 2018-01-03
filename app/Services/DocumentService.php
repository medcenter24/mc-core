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
     * @return Document
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function createDocumentFromFile($file, User $user)
    {
        /** @var Document $document */
        $document = Document::create([
            'created_by' => $user->id,
            'title' => $file->getClientOriginalName()
        ]);
        $document->addMedia($file)
            ->toMediaCollection(DocumentService::CASES_FOLDERS, DocumentService::DISC_IMPORTS);
        $user->documents()->attach($document);

        return $document;
    }

    /**
     * @param $files
     * @param User $user
     * @return Collection
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function createDocumentsFromFiles($files, User $user)
    {
        $documents = new Collection();
        foreach ($files as $file) {
            $document = $this->createDocumentFromFile($file, $user);
            $documents->push($document);
        }
        return $documents;
    }

    /**
     * @param User|null $user
     * @param Accident|null $accident
     * @param string $type - all, user or accident
     * @return Collection
     */
    public function getDocuments(User $user = null, Accident $accident = null, $type = 'all')
    {
        $documents = new Collection();
        if ($user && ($type == 'all' || $type == 'user')) {
            $documents = $documents->merge($user->documents);
        }

        if ($accident && ($type == 'all' || $type == 'accident')) {
            $documents = $documents->merge($accident->documents);
            if ($accident->caseable) {
                $documents = $documents->merge($accident->caseable->documents);
            }
        }

        return $documents->unique('id');
    }

    /**
     * @param Document $document
     * @param User $user
     * @param RoleService $roleService
     * @return bool
     */
    public function checkAccess(Document $document, User $user, RoleService $roleService)
    {
        return $roleService->hasRole($user, 'director')
            || ($roleService->hasRole($user, 'doctor') && $document->created_by == $user->id);
    }

    /**
     * Load docs from accident
     * @param Accident $accident
     * @param string $type
     * @return Collection
     */
    public function getAccidentDocuments(Accident $accident, $type = '')
    {
        $res = new Collection();
        if ($accident->caseable && $accident->caseable->documents) {
            $query = $accident->documents()->orderBy('created_at');
            if ($type) {
                $query->where('type', $type);
            }
            $res = $query->get();
        }
        return $res;
    }

    /**
     * insurances first then passports
     * @param Accident $accident
     * @return Collection
     */
    public function getOrderedAccidentDocuments(Accident $accident)
    {
        return $this->getAccidentDocuments($accident, DocumentService::TYPE_INSURANCE)
            ->merge($this->getAccidentDocuments($accident, DocumentService::TYPE_PASSPORT));
    }
}
