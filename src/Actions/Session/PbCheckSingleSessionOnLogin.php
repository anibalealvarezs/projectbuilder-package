<?php

namespace Anibalealvarezs\Projectbuilder\Actions\Session;

use Anibalealvarezs\Projectbuilder\Models\PbConfig;
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
            redirect()->route('login');
        }
        if (!$user = PbUser::find($request->user()->id)) {
            redirect()->route('login');
        }
        if ((!$lastSession = $user->last_session) || !PbConfig::getValueByKey('_ENABLE_SINGLE_SESSION_')) {
            return $next($request);
        }

        if (($lastSession != $request->session()->getId()) && !PbConfig::getValueByKey('_FORCE_NEW_SESSION_') && !$user->hasRole('super-admin')) {
            if (!PbConfig::getValueByKey('_ALLOW_MULTIPLE_ADMIN_SESSION_') || !$user->hasRole('admin')) {
                auth('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'This user has already started a session.');
            }
        }

        return $next($request);
    }
}
