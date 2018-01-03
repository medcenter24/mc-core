<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Company;
use App\Http\Controllers\ApiController;
use App\Services\LogoService;
use App\Services\SignatureService;
use App\Transformers\CompanyTransformer;
use Illuminate\Http\Request;

class CompaniesController extends ApiController
{
    public function index()
    {
        $companies = Company::orderBy('title')->get();
        return $this->response->collection($companies, new CompanyTransformer());
    }

    public function store(Request $request)
    {
        $company = Company::create([
            'title' => $request->json('title', ''),
        ]);
        \Log::info('Company created', [$company, $this->user()]);
        $transformer = new CompanyTransformer();
        return $this->response->created(null, $transformer->transform($company));
    }

    public function update($id, Request $request)
    {
        $company = Company::findOrFail($id);
        $company->title = $request->json('title', '');
        $company->save();

        \Log::info('Company updated', [$company, $this->user()]);
        return $this->response->item($company, new CompanyTransformer());
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        \Log::info('Company deleted', [$company, $this->user()]);
        $company->delete();
        return $this->response->noContent();
    }

    public function uploadLogo($id)
    {
        $company = Company::findOrFail($id);
        $company->clearMediaCollection(LogoService::FOLDER);
        $company->addMediaFromRequest('file')
            ->toMediaCollection(LogoService::FOLDER, LogoService::DISC);
        return $this->response->created(asset($company->getFirstMediaUrl(LogoService::FOLDER, 'thumb_250')),
            [
                'thumb250' => asset($company->getFirstMediaUrl(LogoService::FOLDER, 'thumb_250')),
                'original' => asset($company->getFirstMediaUrl(LogoService::FOLDER)),
            ]);
    }

    public function uploadSign($id)
    {
        $company = Company::findOrFail($id);
        $company->clearMediaCollection(SignatureService::FOLDER);
        $company->addMediaFromRequest('file')
            ->toMediaCollection(SignatureService::FOLDER, SignatureService::DISC);
        return $this->response->created('/forbidden',
            [
                'signature' => base64_encode(file_get_contents($company->getFirstMedia(SignatureService::FOLDER)->getPath('thumb_300x100'))),
            ]);
    }
}
