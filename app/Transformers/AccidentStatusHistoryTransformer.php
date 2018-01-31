<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\AccidentStatusHistory;
use App\Helpers\MediaHelper;
use App\Services\LogoService;
use App\User;
use League\Fractal\TransformerAbstract;

class AccidentStatusHistoryTransformer extends TransformerAbstract
{
    /**
     * @param AccidentStatusHistory $history
     * @return array
     * @throws \ErrorException
     */
    public function transform(AccidentStatusHistory $history)
    {
        return [
            'id' => $history->id,
            'user_id' => $history->user_id,
            'user_name' => $history->user_id ? $history->user->name : '',
            'user_thumb' => $history->user_id && $history->user->hasMedia(LogoService::FOLDER)
                ? MediaHelper::b64($history->user, LogoService::FOLDER, User::THUMB_45) : '',
            'accident_status_id' => $history->accident_status_id,
            'status' => $history->accidentStatus->title,
            'commentary' => $history->commentary,
            'created_at' => $history->created_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')),
            'updated_at' => $history->updated_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')),
        ];
    }
}
