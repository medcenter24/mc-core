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
use medcenter24\mcCore\App\Services\DoctorLayer\FiltersTrait;
use medcenter24\mcCore\App\Services\Entity\Contracts\CreatedByField;
use medcenter24\mcCore\App\Services\Entity\Traits\Access;
use medcenter24\mcCore\App\Services\Entity\Traits\Diseasable;
use medcenter24\mcCore\App\Services\Entity\Contracts\StatusableService;

class ServiceService extends AbstractModelService implements StatusableService, CreatedByField
{
    use FiltersTrait;
    use Diseasable;
    use Access;

    public const FIELD_ID = 'id';
    public const FIELD_TITLE = 'title';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_STATUS = 'status';
    public const FIELD_CREATED_AT = 'created_at';

    /**
     * That can be modified
     */
    public const FILLABLE = [
        self::FIELD_TITLE,
        self::FIELD_DESCRIPTION,
        self::FIELD_CREATED_BY,
        self::FIELD_STATUS,
    ];

    public const UPDATABLE = [
        self::FIELD_TITLE,
        self::FIELD_DESCRIPTION,
        self::FIELD_STATUS,
    ];

    /**
     * That can be viewed
     */
    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_TITLE,
        self::FIELD_DESCRIPTION,
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
            self::FIELD_CREATED_BY => 0,
            self::FIELD_STATUS => self::STATUS_ACTIVE,
        ];
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data = []): Model
    {
        $data[self::FIELD_CREATED_BY] = auth()->user() ? auth()->user()->getAuthIdentifier() : 0;
        $service = parent::create($data);
        $this->assignDiseases($service, $data);
        return $service;
    }

    public function findAndUpdate(array $filterByFields, array $data): Model
    {
        $service = parent::findAndUpdate($filterByFields, $data);
        $this->assignDiseases($service, $data);
        return $service;
    }
}
