<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Services\LogoService;
use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform (User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'lang' => $user->lang,
            'thumb_200' => $user->hasMedia(LogoService::FOLDER)
                ? asset($user->getFirstMediaUrl(LogoService::FOLDER, 'thumb_200')) : '',
            'thumb_45' => $user->hasMedia(LogoService::FOLDER)
                ? asset($user->getFirstMediaUrl(LogoService::FOLDER, 'thumb_45')) : '',
        ];
    }
}
