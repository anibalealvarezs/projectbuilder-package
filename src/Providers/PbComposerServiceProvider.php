<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\View;

class PbComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Contracts\Http\Kernel $kernel)
    {
        // View Composers
        View::composers([
            'Anibalealvarezs\Projectbuilder\ViewComposers\ScriptsComposer' => ['builder::layouts.front.resources.scripts'],
            'Anibalealvarezs\Projectbuilder\ViewComposers\StylesComposer' => ['builder::layouts.front.resources.styles']
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