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

namespace medcenter24\mcCore\App\Transformers;

use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\MediaHelper;
use medcenter24\mcCore\App\Services\Entity\AbstractModelService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusHistoryService;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Entity\UserService;
use medcenter24\mcCore\App\Services\LogoService;

class AccidentStatusHistoryTransformer extends AbstractTransformer
{
    use ServiceLocatorTrait;

    private const HISTORYABLE_TYPE_MAP = [
        Accident::class => 'accident',
        DoctorAccident::class => 'doctorAccident',
        HospitalAccident::class => 'hospitalAccident',
    ];

    #[ArrayShape([0                  => "string",
                  'userId'           => "string",
                  'accidentStatusId' => "string",
                  'historyableId'    => "string",
                  'historyableType'  => "string",
                  5                  => "string",
                  'createdAt'        => "string",
                  'updatedAt'        => "string"
    ])] protected function getMap(): array
    {
        return [
            AbstractModelService::FIELD_ID,
            'userId' => AccidentStatusHistoryService::FIELD_USER_ID,
            'accidentStatusId' => AccidentStatusHistoryService::FIELD_ACCIDENT_STATUS_ID,
            'historyableId' => AccidentStatusHistoryService::FIELD_HISTORYABLE_ID,
            'historyableType' => AccidentStatusHistoryService::FIELD_HISTORYABLE_TYPE,
            AccidentStatusHistoryService::FIELD_COMMENTARY,
            'createdAt' => AbstractModelService::FIELD_CREATED_AT,
            'updatedAt' => AbstractModelService::FIELD_UPDATED_AT,
        ];
    }

    #[ArrayShape([AbstractModelService::FIELD_ID                         => "string",
                  AccidentStatusHistoryService::FIELD_USER_ID            => "string",
                  AccidentStatusHistoryService::FIELD_ACCIDENT_STATUS_ID => "string",
                  AccidentStatusHistoryService::FIELD_HISTORYABLE_ID     => "string",
                  AbstractModelService::FIELD_CREATED_AT                 => "string",
                  AbstractModelService::FIELD_UPDATED_AT                 => "string"
    ])] protected function getMappedTypes(): array
    {
        return [
            AbstractModelService::FIELD_ID                         => self::VAR_INT,
            AccidentStatusHistoryService::FIELD_USER_ID            => self::VAR_INT,
            AccidentStatusHistoryService::FIELD_ACCIDENT_STATUS_ID => self::VAR_INT,
            AccidentStatusHistoryService::FIELD_HISTORYABLE_ID     => self::VAR_INT,
            AbstractModelService::FIELD_CREATED_AT                 => self::VAR_DATETIME,
            AbstractModelService::FIELD_UPDATED_AT                 => self::VAR_DATETIME,
        ];
    }

    /**
     * @param Model $model
     * @return array
     * @throws InconsistentDataException
     */
    public function transform(Model $model): array
    {
        $fields = parent::transform($model);
        $fields['userName'] = $model->getAttribute('user')
            ? $model->getAttribute('user')->getAttribute('name')
            : '';
        $fields['userThumb'] = $model->getAttribute('user')
            && $model->getAttribute('user')->hasMedia(LogoService::FOLDER)
            ? MediaHelper::b64($model->getAttribute('user'), LogoService::FOLDER, UserService::THUMB_45)
            : '';
        $fields['statusTitle'] = $model->getAttribute('accidentStatus')
            ? $model->getAttribute('accidentStatus')->getAttribute('title')
            : '';
        $fields['historyableType'] = $this->getTransformedHistoryableType($fields['historyableType']);
        return $fields;
    }

    /**
     * @param string $fieldName
     * @return string
     */
    private function getTransformedHistoryableType(string $fieldName): string
    {
        return array_key_exists($fieldName, self::HISTORYABLE_TYPE_MAP)
            ? self::HISTORYABLE_TYPE_MAP[$fieldName]
            : 'undefined';
    }
}
