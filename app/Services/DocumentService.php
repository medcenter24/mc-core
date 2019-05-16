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

namespace medcenter24\mcCore\App\Services;



use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Document;
use medcenter24\mcCore\App\User;
use Illuminate\Support\Collection;

class DocumentService extends AbstractModelService
{
    public const DISC_IMPORTS = 'documents';
    public const CASES_FOLDERS = 'patients';

    public const TYPE_PASSPORT = 'passport';
    public const TYPE_INSURANCE = 'insurance';

    public const TYPES = [self::TYPE_PASSPORT, self::TYPE_INSURANCE];

    protected function getClassName(): string
    {
        return Document::class;
    }

    protected function getRequiredFields(): array
    {
        return [
            'created_by' => 0,
            'title' => '',
        ];
    }

    /**
     * @param $file
     * @param User $user
     * @return Document
     */
    public function createDocumentFromFile($file, User $user): Document
    {
        /** @var Document $document */
        $document = $this->create([
            'created_by' => $user->id,
            'title' => $file->getClientOriginalName()
        ]);
        $document->addMedia($file)
            ->toMediaCollection(self::CASES_FOLDERS, self::DISC_IMPORTS);
        $user->documents()->attach($document);

        return $document;
    }

    /**
     * @param $files
     * @param User $user
     * @return Collection
     */
    public function createDocumentsFromFiles($files, User $user): Collection
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
    public function getDocuments(User $user = null, Accident $accident = null, $type = 'all'): Collection
    {
        $documents = new Collection();
        if ($user && ($type === 'all' || $type === 'user')) {
            $documents = $documents->merge($user->documents);
        }

        if ($accident && ($type === 'all' || $type === 'accident')) {
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
     * @param RoleService $roleService
     * @return bool
     */
    public function checkAccess(Document $document, User $user, RoleService $roleService): bool
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
