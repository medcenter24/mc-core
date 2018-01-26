<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Helpers\MediaHelper;
use App\Services\LogoService;
use App\User;
use Cmgmyr\Messenger\Models\Message;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    /**
     * @param Message $message
     * @return array
     * @throws \ErrorException
     */
    public function transform(Message $message)
    {
        return [
            'id' => $message->id,
            'user_id' => $message->user_id,
            'user_name' => $message->user_id ? $message->user->name : '',
            'user_thumb' => $message->user_id && $message->user->hasMedia(LogoService::FOLDER)
                ? MediaHelper::b64($message->user, LogoService::FOLDER, User::THUMB_45) : '',
            'body' => $message->body,
            'created_at' => $message->created_at->format(config('date.systemFormat')),
        ];
    }
}
