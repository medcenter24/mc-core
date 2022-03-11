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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1;

use Dingo\Api\Http\Response;
use Illuminate\Contracts\Auth\Guard;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\MediaHelper;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Entity\AbstractModelService;
use medcenter24\mcCore\App\Services\Entity\CompanyService;
use medcenter24\mcCore\App\Services\Entity\UserService;
use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\App\Support\Facades\Roles;
use medcenter24\mcCore\App\Transformers\CompanyTransformer;
use medcenter24\mcCore\App\Transformers\UserTransformer;
use medcenter24\mcCore\App\Entity\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;

class AuthenticateController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api', ['except' => ['authenticate']]);
    }

    /**
     * Get current Company (by authenticated user)
     */
    /**
     * @return Response
     */
    public function getCompany(): Response
    {
        $company = $this->user()->company;
        if (!$company) {
            /** @var CompanyService $companyService */
            $companyService = $this->getServiceLocator()->get(CompanyService::class);
            $company = $companyService->create();
            $this->user()->company_id = $company->getAttribute(AbstractModelService::FIELD_ID);
            $this->user()->save();
        }
        return $this->response->item($company, new CompanyTransformer());
    }

    /**
     *  API Login, on success return JWT Auth token
     *
     * @param Request $request
     * @return Response|null|void
     * @throws InconsistentDataException
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
            if (
                $request->header('Origin') === config('api.originDirector')
                || (
                        config('app.debug') === true
                        && $request->header('Origin') === config('api.originDirectorDev')
                )
            ) {
                $hasAccess = Roles::hasRole($this->guard()->user(), RoleService::DIRECTOR_ROLE);
            }

            if (
                $request->header('Origin') === config('api.originDoctor')
                || (
                    config('app.debug') === true
                    && $request->header('Origin') === config('api.originDoctorDev')
                )
            ) {
                $hasAccess = Roles::hasRole($this->guard()->user(), RoleService::DOCTOR_ROLE);
            }

            if ($hasAccess && Roles::hasRole($this->guard()->user(), RoleService::LOGIN_ROLE)) {
                Log::debug('User Has Access, token returned');
                return $this->respondWithToken($token);
            }

            Log::warning('User does not have required access role', ['email' => $credentials->get('email')]);
        } else {
            Log::debug('Incorrect credentials, unauthorized');
        }

        $this->response->errorUnauthorized();
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     */
    public function logout(): Response
    {
        $this->guard()->logout();
        return $this->response->noContent();
    }
    /**
     * Returns the authenticated user
     */
    public function authenticatedUser(): Response
    {
        return $this->response->item($this->guard()->user(), new UserTransformer());
    }

    /**
     * Refresh the token
     *
     * @return Response|null
     * @throws InconsistentDataException
     */
    public function getToken(): ?Response
    {
         try {
             return $this->respondWithToken($this->guard()->refresh());
        } catch (TokenBlacklistedException $e) {
             Log::info('Token blacklisted', [$this->guard()->user()]);
             $this->response->error('Invalid token', 401);
        } catch (TokenExpiredException $e) {
             Log::info('Token expired', [$this->guard()->user()]);
             $this->response->error('Invalid token', 401);
         }
         return null;
    }

    /**
     * Get the token array structure.
     * @param $token
     * @return Response
     * @throws InconsistentDataException
     */
    protected function respondWithToken($token): Response
    {
        /** @var User $user */
        $user = $this->guard()->user();
        return $this->response->accepted(url()->to('/'), [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'lang' => $user->lang,
            'thumb' => $user->hasMedia(LogoService::FOLDER)
                ? MediaHelper::b64($user, LogoService::FOLDER, UserService::THUMB_45)
                : ''
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return Guard
     */
    public function guard(): Guard
    {
        return Auth::guard('api');
    }
}
