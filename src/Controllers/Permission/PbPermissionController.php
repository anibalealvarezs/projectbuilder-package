<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Permission;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Models\PbRole;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Auth;
use DB;

use Inertia\Response as InertiaResponse;

use Session;

class PbPermissionController extends PbBuilderController
{
    public function __construct($crud_perms = false)
    {
        // Vars Override
        $this->key = 'Permission';
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
        $user = PbUser::find(Auth::user()->id);
        $toExclude = ['crud super-admin'];
        if (!$user->hasRole('super-admin')) {
            $toExclude = array_merge($toExclude, ['admin roles '.$this->names, 'manage app']);
            if (!$user->hasRole('admin')) {
                $toExclude = array_merge($toExclude, ['login', 'create users', 'update users', 'delete users']);
            }
        }
        ${$this->names} = $this->modelPath::with('roles')->whereNotIn('name', $toExclude)->get(); //Get all permissions

        $shares = [
            'roles',
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
            'roles',
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
            'name' => ['required', 'max:40'],
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

        $roles = $request['roles'];

        // Process
        try {
            ${$this->name} = new $this->modelPath();
            foreach($keys as $key) {
                ${$this->name}->$key = ${$key};
            }
            ${$this->name}->guard_name = 'admin';
            if (${$this->name}->save()) {
                $adminRoles = PbRole::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                ${$this->name}->syncRoles(
                    array_merge(
                        ($roles && is_array($roles) ? $roles : [$roles]),
                        ($adminRoles && is_array($adminRoles) ? $adminRoles : [$adminRoles])
                    )
                );
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
     * @param array $shares
     * @return InertiaResponse
     */
    public function edit(int $id, $element = null, array $shares = []): InertiaResponse
    {
        ${$this->name} = $this->modelPath::with('roles')->findOrFail($id);

        $shares = [
            'roles',
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
            'name' => ['required', 'max:40'],
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

        $roles = $request['roles'];

        // Process
        try {
            ${$this->name} = $this->modelPath::find($id);
            $requests = [];
            foreach($keys as $key) {
                $requests[$key] = ${$key};
            }
            if (${$this->name}->update($requests)) {
                if (in_array(${$this->name}->name, ['crud super-admin'])) {
                    $superAdminRoles = PbRole::whereIn('name', ['super-admin'])->get()->modelKeys();
                    ${$this->name}->syncRoles($superAdminRoles);
                } elseif (in_array(${$this->name}->name, ['manage app', 'admin roles permissions'])) {
                    $adminRoles = PbRole::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                    ${$this->name}->syncRoles($adminRoles);
                } elseif (in_array(${$this->name}->name, ['login'])) {
                    $adminRoles = PbRole::all()->modelKeys();
                    ${$this->name}->syncRoles($adminRoles);
                } else {
                    $adminRoles = PbRole::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                    ${$this->name}->syncRoles(
                        array_merge(
                            (is_array($roles) ? $roles : [$roles]),
                            (is_array($adminRoles) ? $adminRoles : [$adminRoles])
                        )
                    );
                }
            }

            return $this->redirectResponseCRUDSuccess($request, $this->key.' updated successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be updated!');
        }
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
        // Process
        try {
            ${$this->name} = $this->modelPath::findOrFail($id);
            //Make it impossible to delete these specific permissions
            if (in_array(${$this->name}->name, ['admin roles permissions', 'manage app', 'crud super-admin'])) {
                $request->session()->flash('flash.banner', 'This permission can not be deleted!');
                $request->session()->flash('flash.bannerStyle', 'danger');

                return redirect()->route($this->names . '.index');
            }
            ${$this->name}->delete();

            return $this->redirectResponseCRUDSuccess($request, $this->key.' deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be deleted!');
        }
    }
}
