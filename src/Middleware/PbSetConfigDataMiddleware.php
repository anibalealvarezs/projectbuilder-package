<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use Illuminate\Http\Request;
use Closure;

class PbSetConfigDataMiddleware
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
        if (!isApi($request)) {
            PbConfig::all()->each(function ($item) use ($request) {
                $request->session()->put($item->configkey, $item->configvalue);
            });
        }

        return $next($request);
    }
}
