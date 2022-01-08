<?php

namespace Anibalealvarezs\ProjectBuilder\Helpers;

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

        $package = PbHelpers::getDefault('package');

        Inertia::setRootView($package . '::app');

        return Inertia::render($package . '/' . $page, $data);
    }
}
