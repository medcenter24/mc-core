<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string $role
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->user()) {
            return response('Unauthorized', 401);
        }

        if (!\Roles::hasRole(auth()->user(), $role)) {
            if ( $request->ajax() ) {
                return response('Access denied', 403);
            } else {
                abort(403, trans('Access denied'));
            }
        }

        return $next($request);
    }
}
