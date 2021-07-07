<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Navigation;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Session;

use Inertia\Response as InertiaResponse;

class PbNavigationController extends PbBuilderController
{
    function __construct($crud_perms = false)
    {
        // Vars Override
        $this->key = 'Navigation';
        // Parent construct
        parent::__construct(true);
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
        ${$this->names} = $this->modelPath::with('permission')->get();

        $shares = [
            'permissionsall',
        ];

        return parent::index(${$this->names}, $shares);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param array $shares
     * @return InertiaResponse
     */
    public function create(array $shares = []): InertiaResponse
    {
        $shares = [
            'permissionsall',
        ];

        return parent::create($shares);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param array $validationRules
     * @param array $replacers
     * @return void
     */
    public function store(Request $request, array $validationRules = [], array $replacers = [])
    {
        $validationRules = [
            'name' => ['required', 'max:190'],
            'destiny' => ['required', 'max:254'],
            'type' => ['required', 'max:254', Rule::in(['route', 'path', 'custom'])],
            'parent' => ['required', 'integer'],
            'permission' => ['required', 'integer'],
            'module' => [],
        ];

        $replacers = [
            'permission' => 'permission_id'
        ];

        return parent::store($request, $validationRules, $replacers);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param array $shares
     * @return InertiaResponse
     */
    public function edit(int $id, $element = null, array $shares = []): InertiaResponse
    {
        $shares = [
            'permissionsall',
        ];

        return parent::edit($id, $shares);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @param array $validationRules
     * @param array $replacers
     * @return void
     */
    public function update(Request $request, int $id, array $validationRules = [], array $replacers = [])
    {
        $validationRules = [
            'name' => ['required', 'max:190'],
            'destiny' => ['required', 'max:254'],
            'type' => ['required', 'max:254', Rule::in(['route', 'path', 'custom'])],
            'parent' => ['required', 'integer'],
            'permission' => ['required', 'integer'],
            'module' => [],
        ];

        $replacers = [
            'permission' => 'permission_id'
        ];

        return parent::update($request, $id, $validationRules, $replacers);
    }
}
