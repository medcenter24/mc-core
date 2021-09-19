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
use medcenter24\mcCore\App\Entity\Company;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\MediaHelper;
use medcenter24\mcCore\App\Services\Entity\AbstractModelService;
use medcenter24\mcCore\App\Services\Entity\CompanyService;
use medcenter24\mcCore\App\Services\LogoService;

class CompanyTransformer extends AbstractTransformer
{
    /**
     * @param Model $model
     * @return array
     * @throws InconsistentDataException
     */
    public function transform(Model $model): array
    {
        $fields = parent::transform($model);
        $fields['logo250'] = $model instanceof Company && $model->hasMedia(LogoService::FOLDER)
            ? MediaHelper::b64($model, LogoService::FOLDER, Company::THUMB_250)
        : '';
        return $fields;
    }

    protected function getMap(): array
    {
        return [
            AbstractModelService::FIELD_ID,
            CompanyService::FIELD_TITLE,
        ];
    }
}
