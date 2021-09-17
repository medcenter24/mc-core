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


namespace medcenter24\mcCore\Tests\Feature\Api;

use medcenter24\mcCore\App\Entity\User;
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
            'Access-Control-Allow-Origin' => env('CORS_ALLOW_ORIGIN_DIRECTOR', 'http://localhost:4200'),
            // laravel provides Origin header, don't know why...
            'Origin' => env('CORS_ALLOW_ORIGIN_DIRECTOR', 'http://localhost:4200'),
        ];

        if (!is_null($user)) {
            $token = JWTAuth::fromUser($user);
            JWTAuth::setToken($token);
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $headers;
    }
}
