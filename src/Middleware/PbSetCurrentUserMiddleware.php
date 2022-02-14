<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Session;

class PbSetCurrentUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next): mixed
    {
        if (!$request->user()) {
            return $next($request);
        }

        if ($currentUser = PbUser::current()->withAllPermissions()) {
            App::singleton(PbCurrentUser::class, static fn() => $currentUser);
        }

        return $next($request);
    }
}
