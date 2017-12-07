<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\ApiController;
use App\Services\LogoService;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class AuthenticateController extends ApiController
{
    /**
     *  API Login, on success return JWT Auth token
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = collect(json_decode($request->getContent(), true));
            // attempt to verify the credentials and create a token for the user
            if ($token = $this->guard()->attempt($credentials->only('email', 'password')->toArray())) {
                \Log::info('User logged in', ['email' => $credentials->get('email')]);
                return $this->respondWithToken($token);
            }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     */
    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    /**
     * Returns the authenticated user
     */
    public function authenticatedUser()
    {
        return $this->response->item($this->guard()->user(), new UserTransformer());
    }
    /**
     * Refresh the token
     *
     * @return mixed
     */
    public function getToken()
    {
         try {
             return $this->respondWithToken($this->guard()->refresh());
        } catch (TokenBlacklistedException $e) {
             \Log::debug('Token can not be updated for user', [$this->guard()->user()]);
             return response()->json(['error' => 'Invalid token'], 401);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'lang' => $this->guard()->user()->lang,
            'thumb' => $this->guard()->user()->hasMedia(LogoService::FOLDER)
                ? asset($this->guard()->user()->getFirstMediaUrl(LogoService::FOLDER, 'thumb_45')) : ''
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
