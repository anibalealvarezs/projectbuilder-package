<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Closure;
use Illuminate\Http\Request;

class PbSetLocaleMiddleware
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
        if (!PbHelpers::isApi($request) && $request->session() && (app()->getLocale() != $request->session()->get('locale'))) {
            app()->setLocale($request->session()->get('locale'));
        } else {
            if ($request->user()) {
                if (!$locale = PbUser::find($request->user()->id)->getLocale()) {
                    PbUser::current()->update(['language_id' => PbLanguage::findByCode($locale = config('app.locale'))->id]);
                }
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}