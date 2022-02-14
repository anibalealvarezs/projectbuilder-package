<?php

namespace Anibalealvarezs\Projectbuilder\Overrides\Classes;

use Anibalealvarezs\Projectbuilder\Facades\PbUtilitiesFacade;
use Anibalealvarezs\Projectbuilder\Utilities\PbUtilities;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Jetstream\InertiaManager;

class PbInertiaManager extends InertiaManager
{
    /**
     * Render the given Inertia page.
     *
     * @param Request $request
     * @param  string  $page
     * @param  array  $data
     * @return Response
     */
    public function render(Request $request, string $page, array $data = []): Response
    {
        if (isset($this->renderingCallbacks[$page])) {
            foreach ($this->renderingCallbacks[$page] as $callback) {
                $data = $callback($request, $data);
            }
        }

        $package = app(PbUtilities::class)->package;

        Inertia::setRootView($package . '::app');

        return Inertia::render($package . '/' . $page, $data);
    }
}
