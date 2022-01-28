<?php

namespace Anibalealvarezs\Projectbuilder\Actions\Session;

use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Illuminate\Http\Request;

class PbEnsureLoginIsNotDisabled
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param  callable  $next
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

        if (!PbConfig::getValueByKey('_DISABLE_LOGIN_') || $user->hasAnyRole(['super-admin', 'admin'])) {
            return $next($request);
        }

        auth('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('error', 'You\'re not allowed to login at this moment.');
    }
}
