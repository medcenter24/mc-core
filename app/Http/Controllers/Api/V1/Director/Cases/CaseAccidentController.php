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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases;

use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\CaseRequest;
use medcenter24\mcCore\App\Services\ApiSearch\SearchFieldLogic;
use medcenter24\mcCore\App\Services\CaseServices\CaseSearchFieldLogic;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\App\Transformers\CaseAccidentTransformer;

class CaseAccidentController extends ModelApiController
{
    /**
     * @inheritDoc
     */
    protected function getDataTransformer(): TransformerAbstract
    {
        return new CaseAccidentTransformer();
    }

    /**
     * @inheritDoc
     */
    protected function getModelService(): ModelService
    {
        return $this->getServiceLocator()->get(CaseAccidentService::class);
    }

    protected function getRequestClass(): string
    {
        return CaseRequest::class;
    }

    /**
     * @return SearchFieldLogic
     */
    protected function searchFieldLogic(): SearchFieldLogic
    {
        return $this->getServiceLocator()->get(CaseSearchFieldLogic::class);
    }
}
