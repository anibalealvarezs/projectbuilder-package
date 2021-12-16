<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Barryvdh\Debugbar\Facades\Debugbar;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class PbIsDebugModeEnabledMiddleware
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
        $return = $next($request);

        $isLogged = Auth::check();

        $canDebug = false;

        if ($isLogged) {
            $canDebug = PbUser::find(Auth::user()->id)->hasPermissionTo('developer options');
        }

        if(!PbHelpers::getDebugStatus() || !$isLogged || !$canDebug) {
            Debugbar::disable();
        }

        return $return;


    }
}
