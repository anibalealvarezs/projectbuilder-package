<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Closure;
use Illuminate\Http\Request;
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
        if (!$request->user()) {
            return $next($request);
        }

        if (!$user = PbUser::find($request->user()->id)) {
            redirect()->route('login');
        }

        if (!getConfigValue('_ENABLE_SINGLE_SESSION_')) {
            return $next($request);
        }

        if ($user->last_session == Session::getId()) {
            return $next($request);
        }

        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        if (!$user->hasRole('admin') || ($user->hasRole('admin') && !getConfigValue('_ALLOW_MULTIPLE_ADMIN_SESSION_'))) {
            auth('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Your session is no longer valid.');
        }

        return $next($request);
    }
}
