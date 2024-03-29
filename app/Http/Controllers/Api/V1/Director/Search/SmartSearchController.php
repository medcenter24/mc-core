<?php
/*
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
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Search;

use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\SmartSearchRequest;
use medcenter24\mcCore\App\Services\Entity\SmartSearchService;
use medcenter24\mcCore\App\Transformers\SmartSearchTransformer;

class SmartSearchController extends ModelApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new SmartSearchTransformer();
    }

    protected function getModelService(): ModelService|SmartSearchService
    {
        return $this->getServiceLocator()->get(SmartSearchService::class);
    }

    protected function getRequestClass(): string
    {
        return SmartSearchRequest::class;
    }
}
