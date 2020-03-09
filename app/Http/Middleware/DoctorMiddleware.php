<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Middleware;

use Closure;
use Dingo\Api\Auth\Auth;
use Illuminate\Http\Request;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\App\Support\Facades\Roles;

class DoctorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->user()) {
            return response('Unauthorized', 401);
        }

        $doctor = app(Auth::class)->user()->getAttribute('doctor');
        if (!$doctor || !Roles::hasRole(auth()->user(), RoleService::DOCTOR_ROLE)) {
            if ( $request->ajax() ) {
                return response('Current user should be assigned to a doctor', 403);
            }

            abort(403, trans('Current user should be assigned to a doctor'));
        }

        return $next($request);
    }
}
