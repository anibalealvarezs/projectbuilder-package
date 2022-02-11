<?php

namespace Anibalealvarezs\Projectbuilder\Actions\Session;

use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Carbon\Carbon;
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
            app(PbCurrentUser::class)->update(['language_id' => PbLanguage::findByCode($locale = config('app.locale'))->id]);
        }
        app()->setLocale($locale);
        $request->session()->put('locale', $locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
