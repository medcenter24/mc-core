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
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\DoctorRequest;
use medcenter24\mcCore\App\Http\Requests\Api\DoctorUpdateRequest;
use medcenter24\mcCore\App\Services\Entity\CityService;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\App\Transformers\CityTransformer;
use medcenter24\mcCore\App\Transformers\DoctorTransformer;
use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use League\Fractal\TransformerAbstract;

class DoctorsController extends ModelApiController
{

    protected function getDataTransformer(): TransformerAbstract
    {
        return new DoctorTransformer();
    }

    protected function getModelService(): ModelService
    {
        return $this->getServiceLocator()->get(DoctorService::class);
    }

    protected function getRequestClass(): string
    {
        return DoctorRequest::class;
    }

    protected function getUpdateRequestClass(): string
    {
        return DoctorUpdateRequest::class;
    }

    /**
     * Covered by doctor
     * @param $id
     * @return Response
     */
    public function cities($id): Response
    {
        /** @var Doctor $doctor */
        $doctor = $this->getModelService()->first([DoctorService::FIELD_ID => $id]);
        if (!$doctor) {
            $this->response->errorNotFound();
        }
        return $this->response->collection($doctor->cities, new CityTransformer());
    }

    /**
     * Set doctors cities list
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function setCities($id, Request $request): Response
    {
        /** @var Doctor $doctor */
        $doctor = $this->getModelService()->first([DoctorService::FIELD_ID => $id]);
        if (!$doctor) {
            $this->response->errorNotFound();
        }

        $doctor->cities()->detach();
        $cities = $request->json('cities', []);
        if (count($cities)) {
            $doctor->cities()->attach($cities);
        }
        return $this->response->accepted();
    }

    /**
     * Load Doctors by
     * @param $cityId
     * @return Response
     */
    public function getDoctorsByCity($cityId): Response
    {
        /** @var CityService $cityService */
        $cityService = $this->getServiceLocator()->get(CityService::class);
        $city = $cityService->first([CityService::FIELD_ID => $cityId]);
        if (!$city) {
            $this->response->errorNotFound();
        }
        return $this->response->collection($city->doctors, new DoctorTransformer());
    }
}
