<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Exceptions\PbUserException;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;

class PbIsUserEditableMiddleware
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
        if (PbUser::find($request->route('user'))->isEditableBy(Auth::guard($guard)->user()->id)) {
            return $next($request);
        }

        if (!isApi($request)) {
            throw PbUserException::notEditable();
        }

        return response()->json([
            'success' => false,
            'message' => "You don't have permission to edit this user."
        ], 403);
    }
}
