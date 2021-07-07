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
        $query = $this->modelPath::with('permissions')->whereNotIn('name', ['super-admin']);
        $user = PbUser::find(Auth::user()->id);
        if (!$user->hasRole('super-admin')) {
            $query = $query->whereNotIn('name', ['admin']);
        }
        ${$this->names} = $query->get(); //Get all permissions

        $shares = [
            'permissions',
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
            'permissions',
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
            'name' => ['required', 'max:20', Rule::unique($this->table)],
            'alias' => ['required', 'max:190'],
        ];

        $this->storeValidation = array_merge($this->storeValidation, $validationRules);

        // Validation
        $validator = Validator::make($request->all(), $this->storeValidation);
        $this->validationCheck($validator, $request);

        // Requests
        $keys = [];
        foreach($validationRules as $vrKey => $vr) {
            if (isset($replacers[$vrKey])) {
                ${$replacers[$vrKey]} = $request[$vrKey];
                array_push($keys, $replacers[$vrKey]);
            } else {
                ${$vrKey} = $request[$vrKey];
                array_push($keys, $vrKey);
            }
        }

        $permissions = $request['permissions'];

        // Process
        try {
            ${$this->name} = new $this->modelPath();
            foreach($keys as $key) {
                ${$this->name}->$key = ${$key};
            }
            ${$this->name}->guard_name = 'admin';
            if (${$this->name}->save()) {
                ${$this->name}->syncPermissions($permissions);
            }

            return $this->redirectResponseCRUDSuccess($request, $this->key.' created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be created!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param null $element
     * @param int $id
     * @param array $shares
     * @return InertiaResponse
     */
    public function show(int $id, $element = null, array $shares = []): InertiaResponse
    {
        return $this->edit($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return InertiaResponse
     */
    public function edit(int $id, $element = null, array $shares = []): InertiaResponse
    {
        ${$this->name} = $this->modelPath::with('permissions')->findOrFail($id);

        $shares = [
            'permissions',
        ];

        return parent::edit($id, ${$this->name}, $shares);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, int $id, array $validationRules = [], array $replacers = [])
    {
        $validationRules = [
            'name' => ['required', 'max:20', Rule::unique($this->table)->ignore($id)],
            'alias' => ['required', 'max:190'],
        ];

        $this->updateValidation = array_merge($this->updateValidation, $validationRules);

        // Validation
        $validator = Validator::make($request->all(), $this->updateValidation);
        $this->validationCheck($validator, $request);

        // Requests
        $keys = [];
        foreach($validationRules as $vrKey => $vr) {
            if (isset($replacers[$vrKey])) {
                ${$replacers[$vrKey]} = $request[$vrKey];
                array_push($keys, $replacers[$vrKey]);
            } else {
                ${$vrKey} = $request[$vrKey];
                array_push($keys, $vrKey);
            }
        }

        $permissions = $request['permissions'];

        $me = PbUser::find(Auth::user()->id);
        $optionalPermissions = [];
        if ($me->hasRole('super-admin')) {
            $optionalPermissions = PbPermission::whereIn('name', ['read roles', 'read configs', 'read permissions', 'read navigations'])->get()->modelKeys();
        }
        $adminOptionalPermissions = array_intersect($optionalPermissions, $permissions);
        // Process
        try {
            ${$this->name} = $this->modelPath::find($id);
            $requests = [];
            foreach($keys as $key) {
                $requests[$key] = ${$key};
            }
            if (${$this->name}->update($requests)) {
                if (${$this->name}->name == 'super-admin') {
                    $permissions = PbPermission::all()->modelKeys();
                } elseif (${$this->name}->name == 'admin') {
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
                } elseif (${$this->name}->name == 'user') {
                    $permissions = PbPermission::whereIn('name', ['login'])->get()->modelKeys();
                }
                ${$this->name}->syncPermissions($permissions);
            }

            return $this->redirectResponseCRUDSuccess($request, $this->key.' updated successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be updated!');
        }
    }
}
