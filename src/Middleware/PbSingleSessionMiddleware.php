<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class PbSingleSessionMiddleware
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
        if (Auth::check()) {
            if (Auth::user()->last_session != Session::getId()) {
                Auth::logout();
                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}
