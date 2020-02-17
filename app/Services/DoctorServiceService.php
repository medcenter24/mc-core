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

use medcenter24\mcCore\App\Doctor;
use medcenter24\mcCore\App\DoctorService;
use medcenter24\mcCore\App\Services\DoctorLayer\FiltersTrait;

class DoctorServiceService extends AbstractModelService
{

    use FiltersTrait;

    public const STATUS_ACTIVE = 'active';

    protected function getClassName(): string
    {
        return DoctorService::class;
    }

    protected function getRequiredFields(): array
    {
        return [
            'title' => '',
            'description' => '',
            'created_by' => 0,
            'disease_id' => 0,
        ];
    }

    public function isDoctor(int $userId): bool
    {
        return $userId && Doctor::where('user_id', $userId)->count() > 0;
    }
}
