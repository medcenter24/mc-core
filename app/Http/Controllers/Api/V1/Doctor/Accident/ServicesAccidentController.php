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
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\ServiceService;
use medcenter24\mcCore\App\Transformers\ServiceTransformer;

class ServicesAccidentController extends ApiController
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
     * @return ServiceService
     */
    private function getServiceService(): ServiceService
    {
        return $this->getServiceLocator()->get(ServiceService::class);
    }

    /**
     * @param $id
     * @return Response
     */
    public function services($id): Response
    {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        $this->checkAccess($accident);

        /** @var Collection $services */
        $services = $accident->caseable->services->each(function (Service $service) {
            if ($service->created_by === $this->user()->id) {
                $service->markAsDoctor();
            }
        });

        return $this->response->collection($services, new ServiceTransformer());
    }

    /**
     * @param $id
     * @param Request $request
     * @return Response
     * @throws InconsistentDataException
     */
    public function saveService($id, Request $request): Response
    {
        Log::info('Request to create new services', ['data' => $request->toArray()]);

        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        $this->checkAccess($accident);

        $doctorAccident = $accident->caseable;

        $serviceId = (int) $request->get('id', 0);
        if ($serviceId) {
            /** @var Service $service */
            $service = $this->getServiceService()->first([ServiceService::FIELD_ID => $serviceId]);
            if (!$service) {
                Log::error('Service not found');
                $this->response->errorNotFound();
            }

            if (!$this->getServiceService()->hasAccess($this->user(), $service)) {
                Log::error('Service can not be updated, user has not permissions');
                $this->response->errorMethodNotAllowed();
            }

            $service = $this->getServiceService()->findAndUpdate([ServiceService::FIELD_ID], [
                ServiceService::FIELD_ID => $serviceId,
                ServiceService::FIELD_TITLE => $request->get('title', $service->title),
                ServiceService::FIELD_DESCRIPTION => $request->get('description', $service->description),
                ServiceService::FIELD_STATUS => $request->get('status', ServiceService::STATUS_ACTIVE),
            ]);
        } else {
            /** @var Service $service */
            $service = $this->getServiceService()->create([
                ServiceService::FIELD_TITLE => $request->get('title', ''),
                ServiceService::FIELD_DESCRIPTION => $request->get('description', ''),
                ServiceService::FIELD_CREATED_BY => $this->user()->id,
                ServiceService::FIELD_STATUS => $request->get('status', ServiceService::STATUS_ACTIVE),
            ]);
            $doctorAccident->services()->attach($service);
            $service->markAsDoctor();
        }

        $transformer = new ServiceTransformer();
        return $this->response->accepted(null, $transformer->transform($service));
    }
}
