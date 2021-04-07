<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Models\PbNavigation;
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
    public function boot(\Illuminate\Contracts\Http\Kernel $kernel)
    {
        $aeas = new AeasHelpers();

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
                        'name' => $request->user()->name
                    ] : null,
                ];
            },
            'flash' => function (Request $request) {
                return [
                    'message' => Session::get('message')
                ];
            },
            'navigations' => function () {
                return PbNavigation::with(['ascendant', 'descendants'])->where('parent', 0)->get();
            },
            'navigationsfull' => function () {
                return PbNavigation::all();
            },
            'locale' => function () use ($aeas) {
                $customLocale = $aeas->getCustomLocale();
                if ($customLocale) {
                    return $customLocale;
                }
                return app()->getLocale();
            },
            'languages' => function () {
                return PbLanguage::getEnabled()->get();
            },
            'countries' => function () {
                return PbCountry::all();
            }
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
