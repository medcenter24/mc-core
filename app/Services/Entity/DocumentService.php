<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services\Entity;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Document;
use medcenter24\mcCore\App\Exceptions\CommonException;
use medcenter24\mcCore\App\Entity\User;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class DocumentService extends AbstractModelService
{
    public const DISC_IMPORTS = 'documents';
    public const CASES_FOLDERS = 'patients';

    public const FIELD_TITLE = 'title';
    public const FIELD_CREATED_BY = 'created_by';
    public const FIELD_TYPE = 'type';

    public const RELATION_MEDIA_B64 = 'b64';

    public const TYPE_ALL = 'all';
    public const TYPE_ACCIDENT = 'accident';
    public const TYPE_USER = 'user';

    public const TYPE_PASSPORT = 'passport';
    public const TYPE_INSURANCE = 'insurance';

    public const TYPES = [
        self::TYPE_PASSPORT,
        self::TYPE_INSURANCE,
    ];

    public const FILLABLE = [
        self::FIELD_TITLE,
        self::FIELD_CREATED_BY,
    ];

    public const VISIBLE = [
        self::FIELD_TITLE,
        self::RELATION_MEDIA_B64,
        self::FIELD_CREATED_BY,
    ];

    public const THUMB = 'thumb';
    public const PIC = 'pic';

    protected function getClassName(): string
    {
        return Document::class;
    }

    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
            self::FIELD_CREATED_BY => 0,
        ];
    }

    /**
     * @param UploadedFile $file
     * @param User $user
     * @return Document
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function createDocumentFromFile(UploadedFile $file, User $user): Document
    {
        /** @var Document $document */
        $document = $this->create([
            self::FIELD_CREATED_BY => $user->id,
            self::FIELD_TITLE => $file->getClientOriginalName(),
        ]);
        $document->addMedia($file)
            ->toMediaCollection(self::CASES_FOLDERS, self::DISC_IMPORTS);
        $user->documents()->attach($document);

        return $document;
    }

    /**
     * @param array $files instance of UploadedFile
     * @param User $user
     * @return Collection
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function createDocumentsFromFiles(array $files, User $user): Collection
    {
        $documents = new Collection();
        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $document = $this->createDocumentFromFile($file, $user);
            $documents->push($document);
        }
        return $documents;
    }

    /**
     * @todo move to AccidentDocumentService
     * @param User|null $user
     * @param Accident|null $accident
     * @param string $type - all, user or accident
     * @return Collection
     */
    public function getDocuments(User $user = null, Accident $accident = null, $type = self::TYPE_ALL): Collection
    {
        $documents = new Collection();
        if ($user && in_array($type, [self::TYPE_ALL, self::TYPE_USER], true)) {
            $docs = $user->getAttribute('documents');
            $documents = $documents->merge($docs);
        }

        if ($accident && in_array($type, [self::TYPE_ALL, self::TYPE_ACCIDENT], true)) {
            $documents = $documents->merge($accident->documents);
            if ($accident->getAttribute('caseable')) {
                $documents = $documents->merge($accident->getAttribute('caseable')->documents);
            }
        }

        return $documents->unique('id');
    }

    /**
     * @param Document $document
     * @param User $user
     * @return bool
     */
    public function checkAccess(Document $document, User $user): bool
    {
        $roleService = $this->getServiceLocator()->get(RoleService::class);
        return $roleService->hasRole($user, 'director')
            || ($roleService->hasRole($user, 'doctor') && $document->created_by == $user->id);
    }

    /**
     * @todo move to AccidentDocumentService
     * Load docs from accident
     * @param Accident $accident
     * @param string $type
     * @return Collection
     */
    public function getAccidentDocuments(Accident $accident, $type = ''): Collection
    {
        $res = new Collection();
        if ($accident->caseable && $accident->caseable->documents) {
            $query = $accident->documents()->orderBy(self::FIELD_CREATED_BY);
            if ($type) {
                $query->where(self::FIELD_TYPE, $type);
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
    public function getOrderedAccidentDocuments(Accident $accident): Collection
    {
        return $this->getAccidentDocuments($accident, self::TYPE_INSURANCE)
            ->merge($this->getAccidentDocuments($accident, self::TYPE_PASSPORT));
    }

    /**
     * Changing document type
     * @param Document $document
     * @param string $type
     * @throws CommonException
     */
    public function changeDocType(Document $document, string $type): void
    {
        if (!$this->checkAccess($document, auth()->user())) {
            throw new CommonException('Access denied');
        }
        $document->type = $type;
        $document->save();

        Log::info('Document updated', ['documentId' => $document->id, 'type' => $type]);
    }
}
