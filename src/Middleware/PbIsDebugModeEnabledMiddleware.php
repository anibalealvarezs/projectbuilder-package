<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Facades\PbDebugbarFacade as Debug;
use Closure;
use Illuminate\Http\Request;

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
        Debug::toggleStatus();
        return $next($request);
    }
}
