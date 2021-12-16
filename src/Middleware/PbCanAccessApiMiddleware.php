<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Exceptions\PbUserException;
use Anibalealvarezs\Projectbuilder\Models\PbConfig;
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
        $enabled = (bool) PbConfig::getValueByKey('_API_ENABLED_');

        if ($me = PbUser::find(Auth::guard($guard)->user()->id)) {
            if ($me->hasPermissionTo('api access') && $enabled) {
                return $next($request);
            }
        }

        $message = (!$enabled ? "API service is currently disabled" : "You don't have permission to access the API");

        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 403);
        } else {
            throw PbUserException::custom(403, $message);
        }
    }
}
