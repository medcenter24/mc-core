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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Http\Response;
use Log;
use medcenter24\mcCore\App\Company;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\Transformers\CompanyTransformer;
use Illuminate\Http\Request;

class CompaniesController extends ApiController
{
    public function index(): Response
    {
        $companies = Company::orderBy('title')->get();
        return $this->response->collection($companies, new CompanyTransformer());
    }

    public function store(Request $request): Response
    {
        $company = Company::create([
            'title' => $request->json('title', ''),
        ]);
        Log::info('Company created', [$company, $this->user()]);
        $transformer = new CompanyTransformer();
        return $this->response->created(null, $transformer->transform($company));
    }

    public function update($id, Request $request): Response
    {
        $company = Company::findOrFail($id);
        $company->title = $request->json('title', '');
        $company->save();

        Log::info('Company updated', [$company, $this->user()]);
        return $this->response->item($company, new CompanyTransformer());
    }

    public function destroy($id): Response
    {
        $company = Company::findOrFail($id);
        Log::info('Company deleted', [$company, $this->user()]);
        $company->delete();
        return $this->response->noContent();
    }

    public function uploadLogo($id): Response
    {
        $company = Company::findOrFail($id);
        $company->clearMediaCollection(LogoService::FOLDER);
        $company->addMediaFromRequest('file')
            ->toMediaCollection(LogoService::FOLDER, LogoService::DISC);
        return $this->response->item($company, new CompanyTransformer());
    }

    public function deleteLogo($id): Response
    {
        /** @var Company $company */
        $company = Company::findOrFail($id);
        $company->clearMediaCollection(LogoService::FOLDER);
        return $this->response->noContent();
    }
}
