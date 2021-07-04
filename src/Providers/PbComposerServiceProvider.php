<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class PbComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Kernel $kernel)
    {
        // View Composers
        // View::composers([
            // 'Anibalealvarezs\Projectbuilder\ViewComposers\ScriptsComposer' => ['builder::layouts.front.resources.scripts']
        // ]);
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
