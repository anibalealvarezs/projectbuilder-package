<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Config;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Inertia\Response as InertiaResponse;
use Session;

class PbConfigController extends PbBuilderController
{
    function __construct(Request $request, $crud_perms = false)
    {
        // Vars Override
        $this->key = 'Config';
        // Validation Rules
        $this->validationRules = [
            'name' => ['required', 'max:190'],
            'configvalue' => ['required'],
            'description' => []
        ];
        // Show ID column ?
        $this->showId = false;
        // Parent construct
        parent::__construct($request, true);
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse|RedirectResponse
     */
    public function index(
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): InertiaResponse|JsonResponse|RedirectResponse {
        $arrayElements = $this->modelPath::with('module')->get();

        return parent::index($arrayElements);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $route
     * @return InertiaResponse|JsonResponse
     */
    public function create(string $route = 'level'): InertiaResponse|JsonResponse
    {
        $this->required = array_merge($this->required, ['configkey']);

        return parent::create();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->validationRules['configkey'] = ['required', 'max:50', Rule::unique($this->table)];

        return parent::store($request);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse
     */
    public function edit(
        int $id,
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): InertiaResponse|JsonResponse {
        $this->required = array_merge($this->required, ['configkey']);

        return parent::edit($id);
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
        $this->validationRules['configkey'] = ['required', 'max:50', Rule::unique($this->table)->ignore($id)];

        return parent::update($request, $id);
    }
}
