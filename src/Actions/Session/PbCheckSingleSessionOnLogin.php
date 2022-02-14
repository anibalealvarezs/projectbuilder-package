<?php

namespace Anibalealvarezs\Projectbuilder\Actions\Session;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Illuminate\Http\Request;

class PbCheckSingleSessionOnLogin
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (!$request->user()) {
            to_route('login');
        }
        if (!$user = PbUser::find($request->user()->id)) {
            to_route('login');
        }
        if ((!$lastSession = $user->last_session) || !getConfigValue('_ENABLE_SINGLE_SESSION_')) {
            return $next($request);
        }

        if (($lastSession != $request->session()->getId()) && !session('_FORCE_NEW_SESSION_') && !$user->hasRole('super-admin')) {
            if (!getConfigValue('_ALLOW_MULTIPLE_ADMIN_SESSION_') || !$user->hasRole('admin')) {
                auth('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return to_route('login')->with('error', 'This user has already started a session.');
            }
        }

        return $next($request);
    }
}
