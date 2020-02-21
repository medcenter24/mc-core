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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services;

use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Doctor;
use medcenter24\mcCore\App\DoctorAccident;

class DoctorsService extends AbstractModelService
{

    public const FIELD_ID = 'id';
    public const FIELD_NAME = 'name';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_REF_KEY = 'ref_key';
    public const FIELD_GENDER = 'gender';
    public const FIELD_MEDICAL_BOARD_NUM = 'medical_board_num';
    public const FIELD_USER_ID = 'user_id';

    public const FILLABLE = [
        self::FIELD_NAME,
        self::FIELD_DESCRIPTION,
        self::FIELD_REF_KEY,
        self::FIELD_GENDER,
        self::FIELD_MEDICAL_BOARD_NUM,
        self::FIELD_USER_ID,
    ];

    public const UPDATABLE = [
        self::FIELD_NAME,
        self::FIELD_DESCRIPTION,
        self::FIELD_REF_KEY,
        self::FIELD_GENDER,
        self::FIELD_MEDICAL_BOARD_NUM,
        self::FIELD_USER_ID,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_NAME,
        self::FIELD_DESCRIPTION,
        self::FIELD_REF_KEY,
        self::FIELD_GENDER,
        self::FIELD_MEDICAL_BOARD_NUM,
        self::FIELD_USER_ID,
    ];

    protected function getClassName(): string
    {
        return Doctor::class;
    }

    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_NAME => '',
            self::FIELD_DESCRIPTION => '',
            self::FIELD_REF_KEY => '',
            self::FIELD_GENDER => '',
            self::FIELD_MEDICAL_BOARD_NUM => '',
        ];
    }

    /**
     * @param Doctor $doctor
     * @param Accident $accident
     *
     * @return bool
     */
    public function hasAccess(Doctor $doctor, Accident $accident): bool
    {
        return $accident->getAttribute('caseable') instanceof DoctorAccident
            && (int)$accident->getAttribute('caseable')->getAttribute('doctor_id') === (int)$doctor->getAttribute('id');
    }

    public function isDoctor(int $userId): bool
    {
        return $userId > 0 && Doctor::where(self::FIELD_USER_ID, $userId)->count() > 0;
    }

    protected function getUpdatableFields(): array
    {
        return self::UPDATABLE;
    }
}
