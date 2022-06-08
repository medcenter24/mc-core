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
use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Diagnostic;
use medcenter24\mcCore\App\Entity\Survey;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Accident\Service\AccidentServicesService;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\App\Transformers\AccidentCheckpointTransformer;
use medcenter24\mcCore\App\Transformers\Case\CaseServicesTransformer;
use medcenter24\mcCore\App\Transformers\DiagnosticTransformer;
use medcenter24\mcCore\App\Transformers\ServiceTransformer;
use medcenter24\mcCore\App\Transformers\SurveyTransformer;

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
     * @param $id
     * @param RoleService $roleService
     * @return Response
     */
    public function getDiagnostics($id, RoleService $roleService): Response
    {
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }
        /** @var Collection $accidentDiagnostics */
        $accidentDiagnostics = $accident->caseable->diagnostics;
        $accidentDiagnostics->each(function (Diagnostic $diagnostic) use ($roleService) {
            if ($diagnostic->created_by && $roleService->hasRole($diagnostic->creator, 'doctor')) {
                $diagnostic->markAsDoctor();
            }
        });
        return $this->response->collection($accidentDiagnostics, new DiagnosticTransformer());
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
     * @param $id
     * @param RoleService $roleService
     * @return Response
     */
    public function getSurveys($id, RoleService $roleService): Response
    {
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }
        $accidentSurveys = $accident->caseable->surveys;
        $accidentSurveys->each(function (Survey $doctorSurvey) use ($roleService) {
            if ($doctorSurvey->created_by && $roleService->hasRole($doctorSurvey->creator, 'doctor')) {
                $doctorSurvey->markAsDoctor();
            }
        });
        return $this->response->collection($accidentSurveys, new SurveyTransformer());
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
