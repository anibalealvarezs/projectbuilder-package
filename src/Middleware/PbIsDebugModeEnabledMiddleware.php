<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Overrides\Classes\PbDebugbar;
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
        PbDebugbar::toggleStatus();
        return $next($request);
    }
}
