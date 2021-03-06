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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor;

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Entity\SurveyService;
use medcenter24\mcCore\App\Transformers\SurveyTransformer;

class SurveysController extends ApiController
{
    public function index(): Response
    {
        /** @var SurveyService $service */
        $service = $this->getServiceLocator()->get(SurveyService::class);
        $surveys = $service->getActiveByDoctor(auth()->id());
        return $this->response->collection($surveys, new SurveyTransformer());
    }
}
