<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
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
        if (!isApi($request)) {
            if ($request->session() && $request->session()->get('locale') && (app()->getLocale() != $request->session()->get('locale'))) {
                app()->setLocale($request->session()->get('locale'));
            }
        } elseif ($request->user()) {
            if (!$locale = PbUser::find($request->user()->id)->getLocale()) {
                app(PbCurrentUser::class)->update(['language_id' => PbLanguage::findByCode($locale = config('app.locale'))->id]);
            }
            app()->setLocale($locale);
        }

        if (!app()->getLocale()) {
            app()->setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
