<?php

namespace Anibalealvarezs\Projectbuilder\Actions\Session;

use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Illuminate\Http\Request;

class PbUpdateCurrentLocale
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
        if (!$locale = PbUser::find($request->user()->id)->getLocale()) {
            PbUser::current()->update(['language_id' => PbLanguage::findByCode($locale = config('app.locale'))->id]);
        }
        app()->setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
