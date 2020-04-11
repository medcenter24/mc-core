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
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Transformers\DoctorAccidentStatusTransformer;

class StatusAccidentController extends ApiController
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
     * @return AccidentStatusService
     */
    private function getAccidentStatusService(): AccidentStatusService
    {
        return $this->getServiceLocator()->get(AccidentStatusService::class);
    }

    /**
     * @param $id
     * @param Request $request
     * @return Response
     * @throws InconsistentDataException
     */
    public function reject($id, Request $request): Response
    {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        $this->checkAccess($accident);

        $this->getAccidentService()->setStatus($accident, $this->getAccidentStatusService()->getDoctorRejectedStatus());
        return $this->response->noContent();
    }


    /**
     * Send cases to the director as completed
     * @param Request $request
     * @return Response
     * @throws InconsistentDataException
     */
    public function send(Request $request): Response
    {
        $accidents = $request->get('cases', []);

        if (!is_array($accidents) || !count($accidents)) {
            $this->response->errorBadRequest('Accidents do not provided');
        }

        foreach ($accidents as $accidentId) {
            /** @var Accident $accident */
            $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $accidentId]);
            if (!$accident) {
                Log::warning('Accident has not been found, so it could not be sent to the director',
                    ['accidentId' => $accidentId, 'userId' => $this->user()->id]);
                continue;
            }
            $this->checkAccess($accident);
            $status = $this->getAccidentStatusService()->getDoctorSentStatus();
            $this->getAccidentService()->setStatus($accident, $status, 'Sent by doctor');
        }

        return $this->response->noContent();
    }

    /**
     * Get doctor case inheritance status
     * @param $id
     * @return Response
     */
    public function status($id): Response
    {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        $this->checkAccess($accident);
        return $this->response->item($accident->caseable, new DoctorAccidentStatusTransformer());
    }
}
