<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Dashboard;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use App\Http\Requests;

use Auth;
use DB;
use Illuminate\Http\RedirectResponse;
use Session;

use Inertia\Response as InertiaResponse;

class PbDashboardController extends PbBuilderController
{
    function __construct($crud_perms = false)
    {
        // Parent construct
        parent::__construct();
        // Middlewares
        $this->middleware(['role_or_permission:login']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return void
     */
    public function index($element = null, bool $multiple = false, string $route = 'level')
    {
        return $this->renderView($this->package . '/Dashboard');
    }
}
