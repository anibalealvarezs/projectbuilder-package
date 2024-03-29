<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Facades\PbUtilitiesFacade;
use Anibalealvarezs\Projectbuilder\Facades\PbDebugbarFacade as Debug;
use Anibalealvarezs\Projectbuilder\Overrides\Classes\PbDebugbar;
use Anibalealvarezs\Projectbuilder\Utilities\PbUtilities;
use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class PbAppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if (class_exists(\Inertia\Inertia::class)) {
            \Inertia\Inertia::share([
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
                'locale' => function (Request $request) {
                    if (!app()->getLocale()) {
                        if ($request->session()->has('locale')) {
                            app()->setLocale($request->session()->get('locale'));
                        } else {
                            app()->setLocale('en');
                        }
                    }
                    $locale = getDefaultCountryFromCurrentLocale();
                    Debug::add($locale, 'locale');
                    return $locale;
                },
                'teams' => function () {
                    return (bool) PbConfig::getValueByKey('_TEAM_SELECTOR_');
                },
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('PbUtilities', function () {
            return new PbUtilities();
        });
        $this->app->bind('PbDebugbar', function () {
            return new PbDebugbar();
        });
        $this->app->singleton(PbUtilities::class, static fn() => new PbUtilities());
        $this->app->singleton(PbUtilitiesFacade::class, static fn() => new PbUtilitiesFacade());
    }
}
