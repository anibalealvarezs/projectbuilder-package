<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Exceptions\PbUserException;
use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
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
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!(bool) PbConfig::getValueByKey('_API_ENABLED_')) {
            return response()->json([
                'success' => false,
                'message' => "API service is currently disabled"
            ], 403);
        }

        if (!PbHelpers::isApi($request)) {
            throw PbUserException::custom(403, "Invalid request!");
        }

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to access the API"
            ], 403);
        }


        if (PbUser::current()->hasPermissionTo('api access')) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => "You don't have permission to access the API"
        ], 403);
    }
}
