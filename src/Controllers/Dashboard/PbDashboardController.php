<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Dashboard;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;

use App\Http\Requests;

use Auth;
use DB;
use Session;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PbDashboardController extends PbBuilderController
{
    function __construct()
    {
        // Middlewares
        $this->middleware(['role_or_permission:login']);
        // Variables
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $elements
     * @param array $shares
     * @return InertiaResponse
     */
    public function index($elements = null, array $shares = []): InertiaResponse
    {
        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
            )
        );

        return Inertia::render(AeasHelpers::AEAS_PACKAGE . '/Dashboard');
    }
}
