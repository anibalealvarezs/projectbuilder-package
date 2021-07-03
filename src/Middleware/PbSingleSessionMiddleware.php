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
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check())
        {
            // If current session id is not same with last_session column
            if(Auth::user()->last_session != Session::getId())
            {
                // do logout
                Auth::logout();

                // Redirecto login page
                return redirect('/login');
            }
        }

        return $next($request);
    }
}
