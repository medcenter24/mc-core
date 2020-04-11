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

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Entity\Company;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\CompanyRequest;
use medcenter24\mcCore\App\Services\Entity\CompanyService;
use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\Transformers\CompanyTransformer;

/**
 * Class CompaniesController
 * @package medcenter24\mcCore\App\Http\Controllers\Api\V1\Director
 */
class CompaniesController extends ApiController
{
    private function getCompanyService(): CompanyService
    {
        return $this->getServiceLocator()->get(CompanyService::class);
    }

    public function uploadLogo($id): Response
    {
        /** @var Company $company */
        $company = $this->getCompanyService()->first([CompanyService::FIELD_ID => $id]);
        if (!$company) {
            $this->response->errorNotFound();
        }
        $company->clearMediaCollection(LogoService::FOLDER);
        $company->addMediaFromRequest('file')
            ->toMediaCollection(LogoService::FOLDER, LogoService::DISC);
        return $this->response->item($company, new CompanyTransformer());
    }

    public function deleteLogo($id): Response
    {
        /** @var Company $company */
        $company = $this->getCompanyService()->first([CompanyService::FIELD_ID => $id]);
        if (!$company) {
            $this->response->errorNotFound();
        }
        $company->clearMediaCollection(LogoService::FOLDER);
        return $this->response->noContent();
    }

    /**
     * @param $id
     * @param CompanyRequest $request
     * @return Response
     * @throws InconsistentDataException
     */
    public function update($id, CompanyRequest $request): Response
    {
        /** @var Company $company */
        $company = $this->getCompanyService()->first([CompanyService::FIELD_ID => $id]);
        if (!$company) {
            $this->response->errorNotFound();
        }

        $transformer = new CompanyTransformer();
        $data = $transformer->inverseTransform($request->json()->all());
        $data['id'] = $id; // should be updated the requested only
        $company = $this->getCompanyService()->findAndUpdate([CompanyService::FIELD_ID], $data);
        return $this->response->item($company, $transformer);
    }
}
