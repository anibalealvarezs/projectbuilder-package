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

        Inertia::setRootView(PbHelpers::PB_PACKAGE . '::app');

        return Inertia::render(PbHelpers::PB_PACKAGE . '/' . $page, $data);
    }
}
