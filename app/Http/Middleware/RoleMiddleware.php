<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Middleware;

use App\Support\Facades\Roles;
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
        if (!\Roles::hasRole(auth()->user(), $role)) {
            if ($request->ajax()) {
                return response(trans('content.403'), 403);
            } else {
                abort(403, trans('content.403'));
            }
        }

        return $next($request);
    }
}
