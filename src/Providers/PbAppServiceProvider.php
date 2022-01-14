<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\PbDebugbar;
use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class PbAppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Inertia::share([
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'auth' => function (Request $request) {
                return [
                    'user' => $request->user() ? [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                    ] : null,
                ];
            },
            'flash' => function () {
                return [
                    'message' => Session::get('message')
                ];
            },
            'locale' => function () {
                $locale = PbHelpers::getDefaultCountry(app()->getLocale());
                PbDebugbar::addMessage($locale, 'locale');
                return $locale;
            },
            'teams' => function () {
                return (bool) PbConfig::getValueByKey('_TEAM_SELECTOR_');
            },
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
