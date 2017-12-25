<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Company;
use App\Services\LogoService;
use App\Services\SignatureService;
use League\Fractal\TransformerAbstract;

class CompanyTransformer extends TransformerAbstract
{
    public function transform(Company $company)
    {
        return [
            'id' => $company->id,
            'title' => $company->title,
            'logo250' => $company->hasMedia(LogoService::FOLDER)
                ? asset($company->getFirstMediaUrl(LogoService::FOLDER, 'thumb_250'))
                : '',
            'sign' => $company->hasMedia(SignatureService::FOLDER)
                ? base64_encode(file_get_contents($company->getFirstMediaPath(SignatureService::FOLDER, 'thumb_300x100')))
                : '',
        ];
    }
}
