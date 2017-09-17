<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api;


use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait JwtHeaders
{
    /**
     * Return request headers needed to interact with the API.
     * @param User $user
     * @return array of headers.
     */
    protected function headers(User $user = null)
    {
        $headers = [
            'Accept' => 'application/x.' . env('API_SUBTYPE') . '.' . env('API_VERSION') .'+json',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => env('CORS_ALLOW_ORIGIN', 'http://localhost:4200'),
        ];

        if (!is_null($user)) {
            $token = JWTAuth::fromUser($user);
            JWTAuth::setToken($token);
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $headers;
    }
}
