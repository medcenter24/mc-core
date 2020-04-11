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

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Entity\User;
use medcenter24\mcCore\App\Services\DoctorLayer\FiltersTrait;

class ServiceService extends AbstractModelService
{
    use FiltersTrait;

    public const FIELD_ID = 'id';
    public const FIELD_TITLE = 'title';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_DISEASE_ID = 'disease_id';
    public const FIELD_STATUS = 'status';
    public const FIELD_CREATED_AT = 'created_at';
    public const FIELD_CREATED_BY = 'created_by';

    /**
     * Visible and selectable
     */
    public const STATUS_ACTIVE = 'active';
    /**
     * Visible but not selectable
     */
    public const STATUS_HIDDEN = 'hidden';
    /**
     * Hidden and not accessible
     */
    public const STATUS_DISABLED = 'disabled';

    /**
     * That can be modified
     */
    public const FILLABLE = [
        self::FIELD_TITLE,
        self::FIELD_DESCRIPTION,
        self::FIELD_CREATED_BY,
        self::FIELD_DISEASE_ID,
        self::FIELD_STATUS,
    ];

    public const UPDATABLE = [
        self::FIELD_TITLE,
        self::FIELD_DESCRIPTION,
        self::FIELD_DISEASE_ID,
        self::FIELD_STATUS,
    ];

    /**
     * That can be viewed
     */
    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_TITLE,
        self::FIELD_DESCRIPTION,
        self::FIELD_DISEASE_ID,
        self::FIELD_STATUS,
    ];

    protected function getClassName(): string
    {
        return Service::class;
    }

    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
            self::FIELD_DESCRIPTION => '',
            self::FIELD_DISEASE_ID => 0,
            self::FIELD_CREATED_BY => 0,
            self::FIELD_STATUS => self::STATUS_ACTIVE,
        ];
    }

    public function create(array $data = []): Model
    {
        $data[self::FIELD_CREATED_BY] = auth()->user() ? auth()->user()->getAuthIdentifier() : 0;
        return parent::create($data);
    }

    /**
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public function hasAccess(User $user, Service $service): bool
    {
        $createdBy = (int) $service->getAttribute(self::FIELD_CREATED_BY);
        $userId = (int) $user->getKey();
        $owner = $createdBy && $userId && $createdBy === $userId;
        $hasDirectorRole = $this->getRoleService()->hasRole($user, RoleService::DIRECTOR_ROLE);
        $hasAdminRole = $this->getRoleService()->hasRole($user, RoleService::ADMIN_ROLE);
        return $hasDirectorRole || $hasAdminRole || $owner;
    }

    /**
     * @return RoleService
     */
    private function getRoleService(): RoleService
    {
        return $this->getServiceLocator()->get(RoleService::class);
    }
}