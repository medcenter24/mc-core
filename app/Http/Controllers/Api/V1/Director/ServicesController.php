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

use JetBrains\PhpStorm\Pure;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\ServiceRequest;
use medcenter24\mcCore\App\Http\Requests\Api\ServiceUpdateRequest;
use medcenter24\mcCore\App\Services\Entity\ServiceService;
use medcenter24\mcCore\App\Transformers\ServiceTransformer;
use League\Fractal\TransformerAbstract;

class ServicesController extends ModelApiController
{
    #[Pure] protected function getDataTransformer(): TransformerAbstract
    {
        return new ServiceTransformer();
    }

    /**
     * @inheritDoc
     */
    protected function getModelService(): ModelService
    {
        return $this->getServiceLocator()->get(ServiceService::class);
    }

    protected function getRequestClass(): string
    {
        return ServiceRequest::class;
    }

    protected function getUpdateRequestClass(): string
    {
        return ServiceUpdateRequest::class;
    }
}
