<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Permission;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use App\Http\Requests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Auth;
use DB;

use Inertia\Response as InertiaResponse;

use Session;

class PbRoleController extends PbBuilderController
{
    protected $superAdminExclusivePermissions;

    protected $adminOptionalPermissions;

    public function __construct(Request $request, $crud_perms = false)
    {
        // Vars Override
        $this->key = 'Role';
        // Validation Rules
        $this->validationRules = [
            'alias' => ['required', 'max:190'],
        ];
        // Additional variables to share
        $this->shares = [
            'permissions',
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
        $query = $this->modelPath::with('permissions')->whereNotIn('name', ['super-admin', 'developer', 'api-user']);
        $user = PbUser::find(Auth::user()->id);
        if (!$user->hasRole('super-admin')) {
            $query = $query->whereNotIn('name', ['admin']);
        }
        $model = $query->get(); //Get all permissions

        $this->required = array_merge($this->required, ['name']);

        return parent::index($model);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $route
     * @return InertiaResponse|JsonResponse
     */
    public function create(string $route = 'level'): InertiaResponse|JsonResponse
    {
        $this->required = array_merge($this->required, ['name']);

        return parent::create($route);
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
        if ($failed = $this->validateRequest($this->validationRules, $request)) {
            return $failed;
        }

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
                $this->loadDefaultPermissions();
                $permissions =
                    PbPermission::whereNotIn('id', $this->superAdminExclusivePermissions)
                        ->whereNotIn('id', $this->adminOptionalPermissions)
                        ->whereIn('id', $permissions)->get();
                $model->syncPermissions($permissions);
            }

            return $this->redirectResponseCRUDSuccess($request, $this->key.' created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be created! '.$e->getMessage());
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
        $model = $this->modelPath::with('permissions')->whereNotIn('name', ['super-admin', 'developer', 'api-user'])->findOrFail($id);

        $this->required = array_merge($this->required, ['name']);

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
        if ($failed = $this->validateRequest($this->validationRules, $request)) {
            return $failed;
        }

        $permissions = $request->input('permissions');

        $me = PbUser::find(Auth::user()->id);

        // Process
        try {
            // Build model
            $model = $this->modelPath::find($id);
            // Build requests
            $requests = $this->processModelRequests($this->validationRules, $request, $this->replacers);
            // Update model
            if ($model->update($requests)) {
                $this->loadDefaultPermissions();
                if (
                    (in_array($model->name, ['super-admin', 'admin']) && $me->hasRole('super-admin')) ||
                    (($model->name == 'admin') && $me->hasRole('admin')) ||
                    !in_array($model->name, ['super-admin', 'admin'])
                ) {
                    if ($model->name == 'super-admin') {
                        $permissions = PbPermission::all();
                    } elseif ($model->name == 'admin') {
                        $permissions =
                            PbPermission::whereNotIn('id', $this->superAdminExclusivePermissions)
                                ->whereIn('id', $permissions)->get();
                    } else {
                        $permissions =
                            PbPermission::whereNotIn('id', $this->superAdminExclusivePermissions)
                                ->whereNotIn('id', $this->adminOptionalPermissions)
                                ->whereIn('id', $permissions)->get();
                    }
                    $model->syncPermissions($permissions);
                }
            }

            return $this->redirectResponseCRUDSuccess($request, $this->key.' updated successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be updated! '.$e->getMessage());
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
            if (in_array($model->name, [
                'user',
                'api-user',
                'admin',
                'developer',
                'super-admin',
            ])) {
                $request->session()->flash('flash.banner', 'This role can not be deleted!');
                $request->session()->flash('flash.bannerStyle', 'danger');

                return redirect()->route($this->names . '.index');
            }
            $model->delete();

            return $this->redirectResponseCRUDSuccess($request, $this->key . ' deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key . ' could not be deleted! '.$e->getMessage());
        }
    }

    protected function loadDefaultPermissions()
    {
        $this->superAdminExclusivePermissions =
            PbPermission::whereIn(
                'name',
                [
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
                    'developer options',
                    'read loggers',
                    'delete loggers',
                    'config loggers',
                    'api access',
                ]
            )->get()->modelKeys();
        $this->adminOptionalPermissions =
            PbPermission::whereIn(
                'name',
                [
                    'read roles',
                    'read configs',
                    'read permissions',
                    'read navigations'
                ]
            )->get()->modelKeys();
    }
}
