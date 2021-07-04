<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Dashboard;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Auth;
use DB;
use Session;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PbDashboardController extends Controller
{
    protected $name;
    protected $table;

    use PbControllerTrait;

    function __construct()
    {
        // Middlewares
        $this->middleware(['role_or_permission:login']);
        // Variables
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
            )
        );

        return Inertia::render(AeasHelpers::AEAS_PACKAGE . '/Dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @throws ValidationException
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return InertiaResponse
     */
    public function show(int $id): InertiaResponse
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return InertiaResponse
     */
    public function edit(int $id): InertiaResponse
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function destroy(Request $request, int $id)
    {
        //
    }
}
