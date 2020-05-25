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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\AssistantRequest;
use medcenter24\mcCore\App\Http\Requests\Api\AssistantUpdateRequest;
use medcenter24\mcCore\App\Services\Entity\AssistantService;
use medcenter24\mcCore\App\Transformers\AssistantTransformer;
use League\Fractal\TransformerAbstract;

class AssistantsController extends ModelApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new AssistantTransformer();
    }

    /**
     * @return ModelService
     */
    protected function getModelService(): ModelService
    {
        return $this->getServiceLocator()->get(AssistantService::class);
    }

    protected function getRequestClass(): string
    {
        return AssistantRequest::class;
    }

    protected function getUpdateRequestClass(): string
    {
        return AssistantUpdateRequest::class;
    }
}
