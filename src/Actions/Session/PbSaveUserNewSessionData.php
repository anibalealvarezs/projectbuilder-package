<?php

namespace Anibalealvarezs\Projectbuilder\Actions\Session;

use Anibalealvarezs\Projectbuilder\Models\PbUser;

class PbSaveUserNewSessionData
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        $user = PbUser::find($request->user()->id);
        $user->update([
            'last_session' => $request->session()->getId(),
        ]);

        return $next($request);
    }
}
