<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Dashboard;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Auth;
use DB;
use Session;

use Inertia\Response as InertiaResponse;

class PbDashboardController extends PbBuilderController
{
    function __construct(Request $request, $crud_perms = false)
    {
        // Parent construct
        parent::__construct($request, $crud_perms);
        // Middlewares
        $this->middleware(['role_or_permission:login']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $page
     * @param int $perpage
     * @param string|null $orderby
     * @param string $field
     * @param string $order
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse|RedirectResponse
     */
    public function index(
        int $page = 1,
        int $perpage = 0,
        string $orderby = null,
        string $field = 'id',
        string $order = 'asc',
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): InertiaResponse|JsonResponse|RedirectResponse {

        return $this->renderResponse($this->vars->helper->package . '/Dashboard');
    }
}
