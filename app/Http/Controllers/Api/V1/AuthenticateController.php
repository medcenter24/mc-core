<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1;


use App\Company;
use App\Helpers\MediaHelper;
use App\Http\Controllers\ApiController;
use App\Services\LogoService;
use App\Services\RoleService;
use App\Transformers\CompanyTransformer;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class AuthenticateController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['authenticate']]);
    }

    /**
     * Get current Company (by authenticated user)
     */
    public function getCompany()
    {
        $company = $this->user()->company;
        if (!$company) {
            $company = Company::create(['title' => '']);
            $this->user()->company_id = $company->id;
            $this->user()->save();
        }
        return $this->response->item($company, new CompanyTransformer());
    }

    /**
     *  API Login, on success return JWT Auth token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = collect(json_decode($request->getContent(), true));

        // attempt to verify the credentials and create a token for the user
        if ($token = $this->guard()->attempt($credentials->only('email', 'password')->toArray())) {
            Log::info('User logged in', ['email' => $credentials->get('email')]);

            // check roles for the allowed origin
            $hasAccess = false;
            switch ($request->header('Origin')) {
                case env('CORS_ALLOW_ORIGIN_DIRECTOR'):
                    $hasAccess = \Roles::hasRole($this->guard()->user(), RoleService::DIRECTOR_ROLE);
                    break;
                case env('CORS_ALLOW_ORIGIN_DOCTOR'):
                    $hasAccess = \Roles::hasRole($this->guard()->user(), RoleService::DOCTOR_ROLE);
                    break;
            }

            if ($hasAccess) {
                return $this->respondWithToken($token);
            } else {
                Log::warning('User does not have required access role', ['email' => $credentials->get('email')]);
            }
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    public function getToken()
    {
         try {
             return $this->respondWithToken($this->guard()->refresh());
        } catch (TokenBlacklistedException $e) {
             Log::debug('Token can not be updated for user', [$this->guard()->user()]);
             return response()->json(['error' => 'Invalid token'], 401);
        }
    }

    /**
     * Get the token array structure.
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'lang' => $this->guard()->user()->lang,
            'thumb' => $this->guard()->user()->hasMedia(LogoService::FOLDER)
                ? MediaHelper::b64($this->guard()->user(), LogoService::FOLDER, User::THUMB_45) : ''
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }
}
