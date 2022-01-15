<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Exceptions\PbUserException;
use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;

class PbIsUserDeletableMiddleware
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
        if (PbUser::find($request->route('user'))->isDeletableBy(Auth::guard($guard)->user()->id)) {
            return $next($request);
        }

        if (!PbHelpers::isApi($request)) {
            throw PbUserException::notDeletable();
        }

        return response()->json([
            'success' => false,
            'message' => "You don't have permission to delete this user."
        ], 403);
    }
}
