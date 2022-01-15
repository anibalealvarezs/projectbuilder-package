<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Exceptions\PbUserException;
use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;

class PbIsUserViewableMiddleware
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
        if ($user = PbUser::find($request->route('user'))) {
            if ($user->isViewableBy(Auth::guard($guard)->user()->id)) {
                return $next($request);
            }
        }

        if (!PbHelpers::isApi($request)) {
            throw PbUserException::notViewable();
        }

        return response()->json([
            'success' => false,
            'message' => "You don't have permission to view this user."
        ], 403);
    }
}
