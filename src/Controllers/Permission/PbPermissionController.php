<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Permission;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Models\PbRole;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use App\Http\Requests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;

use Auth;
use DB;

use Inertia\Response as InertiaResponse;

use Session;

class PbPermissionController extends PbBuilderController
{
    public function __construct(Request $request, $crud_perms = false)
    {
        // Vars Override
        $this->key = 'Permission';
        // Validation Rules
        $this->validationRules = [
            'name' => ['required', 'max:40'],
            'alias' => ['required', 'max:190'],
        ];
        // Additional variables to share
        $this->shares = [
            'roles',
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
    public function index($element = null, bool $multiple = false, string $route = 'level'): InertiaResponse|JsonResponse|RedirectResponse
    {
        $me = PbUser::find(Auth::user()->id);
        $toExclude = ['crud super-admin'];
        if (!$me->hasRole('super-admin')) {
            $toExclude = array_merge($toExclude, ['admin roles '.$this->names, 'manage app']);
            if (!$me->hasRole('admin')) {
                $toExclude = array_merge($toExclude, ['login', 'create users', 'update users', 'delete users']);
            }
        }
        $model = $this->modelPath::with(['roles', 'module'])->whereNotIn('name', $toExclude)->get(); //Get all permissions

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
        // Validation
        if ($failed = $this->validateRequest($this->validationRules, $request)) {
            return $failed;
        }

        $roles = $request->input('roles');

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
                $adminRoles = PbRole::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                $model->syncRoles(
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
     * @param int $id
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return Application|RedirectResponse|Redirector|InertiaResponse|JsonResponse
     */
    public function show(int $id, $element = null, bool $multiple = false, string $route = 'level'): Application|RedirectResponse|Redirector|InertiaResponse|JsonResponse
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
     * @return InertiaResponse|JsonResponse
     */
    public function edit(int $id, $element = null, bool $multiple = false, string $route = 'level'): InertiaResponse|JsonResponse
    {
        $model = $this->modelPath::with('roles')->findOrFail($id);

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
        // Validation
        if ($failed = $this->validateRequest($this->validationRules, $request)) {
            return $failed;
        }

        $roles = $request->input('roles');

        // Process
        try {
            // Build model
            $model = $this->modelPath::find($id);
            // Build requests
            $requests = $this->processModelRequests($this->validationRules, $request, $this->replacers);
            // Update model
            if ($model->update($requests)) {
                if (in_array($model->name, ['crud super-admin'])) {
                    $superAdminRoles = PbRole::whereIn('name', ['super-admin'])->get()->modelKeys();
                    $model->syncRoles($superAdminRoles);
                } elseif (in_array($model->name, ['manage app', 'admin roles permissions'])) {
                    $adminRoles = PbRole::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                    $model->syncRoles($adminRoles);
                } elseif (in_array($model->name, ['login'])) {
                    $adminRoles = PbRole::all()->modelKeys();
                    $model->syncRoles($adminRoles);
                } else {
                    $adminRoles = PbRole::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                    $model->syncRoles(
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
            $model = $this->modelPath::findOrFail($id);
            //Make it impossible to delete these specific permissions
            if (in_array($model->name, ['admin roles permissions', 'manage app', 'crud super-admin'])) {
                $request->session()->flash('flash.banner', 'This permission can not be deleted!');
                $request->session()->flash('flash.bannerStyle', 'danger');

                return redirect()->route($this->names . '.index');
            }
            $model->delete();

            return $this->redirectResponseCRUDSuccess($request, $this->key.' deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be deleted!');
        }
    }
}
