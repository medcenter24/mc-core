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

use medcenter24\mcCore\App\DoctorService;
use medcenter24\mcCore\App\Services\DoctorLayer\FiltersTrait;

class DoctorServiceService extends AbstractModelService
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
    private const STATUS_ACTIVE = 'active';
    /**
     * Visible but not selectable
     */
    private const STATUS_HIDDEN = 'hidden';
    /**
     * Hidden and not accessible
     */
    private const STATUS_DISABLED = 'disabled';

    /**
     * That can be modified
     */
    public const FILLABLE = [
        DoctorSurveyService::FIELD_TITLE,
        DoctorSurveyService::FIELD_DESCRIPTION,
        DoctorSurveyService::FIELD_CREATED_BY,
        DoctorSurveyService::FIELD_DISEASE_ID,
        DoctorSurveyService::FIELD_STATUS,
    ];

    public const UPDATABLE = [
        DoctorSurveyService::FIELD_TITLE,
        DoctorSurveyService::FIELD_DESCRIPTION,
        DoctorSurveyService::FIELD_DISEASE_ID,
        DoctorSurveyService::FIELD_STATUS,
    ];

    /**
     * That can be viewed
     */
    public const VISIBLE = [
        DoctorSurveyService::FIELD_ID,
        DoctorSurveyService::FIELD_TITLE,
        DoctorSurveyService::FIELD_DESCRIPTION,
        DoctorSurveyService::FIELD_DISEASE_ID,
        DoctorSurveyService::FIELD_STATUS,
    ];

    protected function getClassName(): string
    {
        return DoctorService::class;
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
}
