<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Config;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Inertia\Response;
use Session;

class PbConfigController extends PbBuilderController
{
    function __construct($crud_perms = false)
    {
        // Vars Override
        $this->key = 'Config';
        // Parent construct
        parent::__construct(true);
        // Validation Rules
        $this->validationRules = [
            'name' => ['required', 'max:190'],
            'configvalue' => ['required'],
            'description' => []
        ];
        // Show ID column ?
        $this->showId = false;
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return Response
     */
    public function index($element = null, bool $multiple = false, string $route = 'level'): Response
    {
        $arrayElements = $this->modelPath::with('module')->get();

        return parent::index($arrayElements);
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

        return parent::store($request, $this->validationRules, $this->replacers);
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

        return parent::update($request, $id, $this->validationRules, $this->replacers);
    }
}
