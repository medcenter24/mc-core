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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident;

use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Diagnostic;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\DiagnosticService;
use medcenter24\mcCore\App\Transformers\DiagnosticTransformer;

class DiagnosticsAccidentController extends ApiController
{
    use DoctorAccidentControllerTrait;

    /**
     * @return AccidentService
     */
    private function getAccidentService(): AccidentService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }

    /**
     * @return DiagnosticService
     */
    private function getDiagnosticService(): DiagnosticService
    {
        return $this->getServiceLocator()->get(DiagnosticService::class);
    }

    /**
     * @param $id
     * @return Response
     */
    public function diagnostics($id): Response
    {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        $this->checkAccess($accident);

        /** @var Collection $diagnostics */
        $diagnostics = $accident->caseable->diagnostics->each(function (Diagnostic $diagnostic) {
            if ($diagnostic->created_by === $this->user()->id) {
                $diagnostic->markAsDoctor();
            }
        });

        return $this->response->collection($diagnostics, new DiagnosticTransformer());
    }

    /**
     * @param $id
     * @param Request $request
     * @return Response
     * @throws InconsistentDataException
     */
    public function saveDiagnostic($id, Request $request)
    {
        Log::info('Request to create new diagnostic', ['data' => $request->toArray()]);

        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        $this->checkAccess($accident);

        $doctorAccident = $accident->caseable;

        $diagnosticId = $request->get('id');
        if ($diagnosticId) {
            /** @var Diagnostic $diagnostic */
            $diagnostic = $this->getDiagnosticService()->first([DiagnosticService::FIELD_ID => $diagnosticId]);
            if (!$diagnostic) {
                Log::error('Diagnostic not found');
                $this->response->errorNotFound();
            }

            if (!$this->getDiagnosticService()->hasAccess($this->user(), $diagnostic)) {
                Log::error('Diagnostic can not be updated, user has not permissions');
                $this->response->errorMethodNotAllowed();
            }

            $diagnostic = $this->getDiagnosticService()->findAndUpdate([DiagnosticService::FIELD_ID], [
                DiagnosticService::FIELD_ID => $diagnosticId,
                DiagnosticService::FIELD_TITLE => $request->get('title', $diagnostic->title),
                DiagnosticService::FIELD_DESCRIPTION => $request->get('description', $diagnostic->description),
                DiagnosticService::FIELD_DIAGNOSTIC_CATEGORY_ID => $request->get('diagnosticCategoryId', 0),
                DiagnosticService::FIELD_DISEASE_ID => $request->get('diseaseId', 0),
                DiagnosticService::FIELD_STATUS => $request->get('status', DiagnosticService::STATUS_ACTIVE),
            ]);
        } else {
            /** @var Diagnostic $diagnostic */
            $diagnostic = $this->getDiagnosticService()->create([
                DiagnosticService::FIELD_TITLE => $request->get('title', ''),
                DiagnosticService::FIELD_DESCRIPTION => $request->get('description', ''),
                DiagnosticService::FIELD_CREATED_BY => $this->user()->id,
                DiagnosticService::FIELD_DIAGNOSTIC_CATEGORY_ID => $request->get('diagnosticCategoryId', 0),
                DiagnosticService::FIELD_DISEASE_ID => $request->get('diseaseId', 0),
                DiagnosticService::FIELD_STATUS => $request->get('status', DiagnosticService::STATUS_ACTIVE),
            ]);

            $doctorAccident->diagnostics()->attach($diagnostic);
            $diagnostic->markAsDoctor();
        }

        $transformer = new DiagnosticTransformer();
        return $this->response->accepted(null, $transformer->transform($diagnostic));
    }
}
