<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Permission;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Auth;
use DB;

use Inertia\Response as InertiaResponse;

use Session;

class PbRoleController extends PbBuilderController
{
    public function __construct($crud_perms = false)
    {
        // Vars Override
        $this->key = 'Role';
        // Parent construct
        parent::__construct(true);
        // Validation Rules
        $this->validationRules = [
            'alias' => ['required', 'max:190'],
        ];
        // Additional variables to share
        $this->shares = [
            'permissions',
        ];
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
        $query = $this->modelPath::with('permissions')->whereNotIn('name', ['super-admin']);
        $user = PbUser::find(Auth::user()->id);
        if (!$user->hasRole('super-admin')) {
            $query = $query->whereNotIn('name', ['admin']);
        }
        $model = $query->get(); //Get all permissions

        return parent::index($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->validationRules['name'] = ['required', 'max:20', Rule::unique($this->table)];

        // Validation
        $this->validateRequest('store', $this->validationRules, $request);

        $permissions = $request->input('permissions');

        // Process
        try {
            // Build model
            $model = new $this->modelPath();
            // Add requests
            $model = $this->processModelRequests($this->validationRules, $request, $this->replacers, $model);
            // Add additional fields values
            $model->guard_name = 'admin';
            // Model save
            if ($model->save()) {
                $model->syncPermissions($permissions);
            }

            return $this->redirectResponseCRUDSuccess($request, $this->key.' created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be created!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse
     */
    public function show(int $id, $element = null, bool $multiple = false, string $route = 'level'): InertiaResponse
    {
        return $this->edit($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse
     */
    public function edit(int $id, $element = null, bool $multiple = false, string $route = 'level'): InertiaResponse
    {
        $model = $this->modelPath::with('permissions')->findOrFail($id);

        return parent::edit($id, $model);
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
        $this->validationRules['name'] = ['required', 'max:20', Rule::unique($this->table)->ignore($id)];

        // Validation
        $this->validateRequest('update', $this->validationRules, $request);

        $permissions = $request->input('permissions');

        $optionalPermissions = [];
        $me = PbUser::find(Auth::user()->id);
        if ($me->hasRole('super-admin')) {
            $optionalPermissions = PbPermission::whereIn('name', ['read roles', 'read configs', 'read permissions', 'read navigations'])->get()->modelKeys();
        }
        $adminOptionalPermissions = array_intersect($optionalPermissions, $permissions);

        // Process
        try {
            // Build model
            $model = $this->modelPath::find($id);
            // Build requests
            $requests = $this->processModelRequests($this->validationRules, $request, $this->replacers);
            // Update model
            if ($model->update($requests)) {
                if ($model->name == 'super-admin') {
                    $permissions = PbPermission::all()->modelKeys();
                } elseif ($model->name == 'admin') {
                    $permissions = PbPermission::whereNotIn('name', array_merge([
                        'crud super-admin',
                        'admin roles permissions',
                        'read roles',
                        'create roles',
                        'update roles',
                        'delete roles',
                        'read permissions',
                        'create permissions',
                        'update permissions',
                        'delete permissions',
                        'read configs',
                        'create configs',
                        'update configs',
                        'delete configs',
                        'read navigations',
                        'create navigations',
                        'update navigations',
                        'delete navigations',
                    ], $adminOptionalPermissions))->get()->modelKeys();
                } elseif ($model->name == 'user') {
                    $permissions = PbPermission::whereIn('name', ['login'])->get()->modelKeys();
                }
                $model->syncPermissions($permissions);
            }

            return $this->redirectResponseCRUDSuccess($request, $this->key.' updated successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be updated!');
        }
    }
}
