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
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Entity\Region;
use medcenter24\mcCore\App\Http\Requests\Api\RegionRequest;
use medcenter24\mcCore\App\Http\Requests\Api\RegionUpdateRequest;
use medcenter24\mcCore\App\Services\Entity\RegionService;
use medcenter24\mcCore\App\Transformers\RegionTransformer;

class RegionsController extends ModelApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new RegionTransformer();
    }

    protected function getModelService(): ModelService
    {
        return $this->getServiceLocator()->get(RegionService::class);
    }

    protected function getRequestClass(): string
    {
        return RegionRequest::class;
    }

    protected function getUpdateRequestClass(): string
    {
        return RegionUpdateRequest::class;
    }
}
