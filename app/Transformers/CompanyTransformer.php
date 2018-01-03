<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Company;
use App\Helpers\MediaHelper;
use App\Services\LogoService;
use App\Services\SignatureService;
use League\Fractal\TransformerAbstract;

class CompanyTransformer extends TransformerAbstract
{
    /**
     * @param Company $company
     * @return array
     * @throws \ErrorException
     */
    public function transform(Company $company)
    {
        return [
            'id' => $company->id,
            'title' => $company->title,
            'logo250' => $company->hasMedia(LogoService::FOLDER)
                ? MediaHelper::b64($company, LogoService::FOLDER, Company::THUMB_250)
                : '',
            'sign' => $company->hasMedia(SignatureService::FOLDER)
                ? MediaHelper::b64($company, SignatureService::FOLDER, Company::THUMB_300X100)
                : '',
        ];
    }
}
