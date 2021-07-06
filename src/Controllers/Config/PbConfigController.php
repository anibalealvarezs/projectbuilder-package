<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Config;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Session;

class PbConfigController extends PbBuilderController
{
    function __construct()
    {
        // Vars Override
        $this->key = 'Config';
        // Parent construct
        parent::__construct();
        // Middlewares
        $this->middleware(['role_or_permission:read '.$this->names]);
        $this->middleware(['role_or_permission:create '.$this->names])->only('create', 'store');
        $this->middleware(['role_or_permission:update '.$this->names])->only('edit', 'update');
        $this->middleware(['role_or_permission:delete '.$this->names])->only('destroy');
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
            'configkey' => ['required', 'max:50', Rule::unique($this->table)],
            'configvalue' => ['required'],
            'description' => []
        ];

        return parent::store($request, $validationRules, $replacers);
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
            'configkey' => ['required', 'max:50', Rule::unique('config')->ignore($id)],
            'configvalue' => ['required'],
            'description' => []
        ];

        return parent::update($request, $id, $validationRules, $replacers);
    }
}
