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

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Accident\Diagnostic\AccidentDiagnosticService;
use medcenter24\mcCore\App\Services\Accident\Service\AccidentServicesService;
use medcenter24\mcCore\App\Services\Accident\Survey\AccidentSurveyService;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Transformers\AccidentCheckpointTransformer;
use medcenter24\mcCore\App\Transformers\Case\CaseServicesTransformer;

class CaseSourceController extends ApiController
{
    /**
     * @return AccidentService
     */
    private function getAccidentService(): AccidentService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }

    /**
     * @param int $id
     * @param AccidentDiagnosticService $accidentDiagnosticService
     * @return Response
     */
    public function getDiagnostics(
        int $id,
        AccidentDiagnosticService $accidentDiagnosticService,
    ): Response {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->collection(
            $accidentDiagnosticService->getAccidentDiagnostics($accident),
            new CaseServicesTransformer(),
        );
    }

    /**
     * @param int $id
     * @param AccidentServicesService $accidentServices
     * @return Response
     */
    public function getServices(
        int $id,
        AccidentServicesService $accidentServices
    ): Response {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->collection(
            $accidentServices->getAccidentServices($accident),
            new CaseServicesTransformer(),
        );
    }

    /**
     * @param int $id
     * @param AccidentSurveyService $accidentSurvey
     * @return Response
     */
    public function getSurveys(int $id, AccidentSurveyService $accidentSurvey): Response
    {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->collection(
            $accidentSurvey->getAccidentSurveys($accident),
            new CaseServicesTransformer(),
        );
    }

    /**
     * @param $id
     * @return Response
     */
    public function getCheckpoints($id): Response
    {
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }
        return $this->response->collection($accident->checkpoints, new AccidentCheckpointTransformer());
    }
}
