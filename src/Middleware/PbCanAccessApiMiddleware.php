<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Exceptions\PbUserException;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;

class PbCanAccessApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if ($me = PbUser::find(Auth::guard($guard)->user()->id)) {
            if ($me->hasPermissionTo('api access')) {
                return $next($request);
            }
        }

        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to access the API."
            ], 403);
        } else {
            throw PbUserException::custom(403, "You don't have permission to access the API.");
        }
    }
}
