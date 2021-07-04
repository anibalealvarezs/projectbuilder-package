<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Permission;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;
use Anibalealvarezs\Projectbuilder\Models\PbRoles;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Auth;
use DB;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

use Session;

class PbRoleController extends Controller
{
    protected $name;
    protected $table;

    use PbControllerTrait;

    public function __construct()
    {
        // Middlewares
        $this->middleware(['role_or_permission:read roles']);
        $this->middleware(['role_or_permission:create roles'])->only('create', 'store');
        $this->middleware(['role_or_permission:update roles'])->only('edit', 'update');
        $this->middleware(['role_or_permission:delete roles'])->only('destroy');
        // Variables
        $this->name = "roles";
        $this->table = (new PbRoles)->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $rolesQuery = PbRoles::with('permissions')->whereNotIn('name', ['super-admin']);
        $user = PbUser::find(Auth::user()->id);
        if (!$user->hasRole('super-admin')) {
            $rolesQuery = $rolesQuery->whereNotIn('name', ['admin']);
        }
        $roles = $rolesQuery->get(); //Get all permissions

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'permissions',
                ]),
                Shares::allowed([
                    'create roles' => 'create',
                    'update roles' => 'update',
                    'delete roles' => 'delete',
                ]),
            )
        );

        return Inertia::render(AeasHelpers::AEAS_PACKAGE . '/Roles/Roles', [
            'pbroles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'permissions',
                ]),
            )
        );

        return Inertia::render(AeasHelpers::AEAS_PACKAGE . '/Roles/CreateRole');
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
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:20', Rule::unique($this->table)],
            'alias' => ['required', 'max:190'],
            /* 'status' => ['required'], */
        ]);
        $this->validationCheck($validator, $request);

        // Requests
        $name = $request['name'];
        $permissions = $request['permissions'];
        $alias = $request['alias'];

        // Process
        try {
            $role = new PbRoles();
            $role->name = $name;
            $role->alias = $alias;
            $role->guard_name = 'admin';
            if ($role->save()) {
                $role->syncPermissions($permissions);
            }

            return $this->redirectResponseCRUDSuccess($request, 'Role created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'Role could not be created!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return InertiaResponse
     */
    public function show(int $id): InertiaResponse
    {
        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
            )
        );

        return redirect()->route($this->name . '.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return InertiaResponse
     */
    public function edit(int $id): InertiaResponse
    {
        $role = PbRoles::with('permissions')->findOrFail($id);

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'permissions',
                ]),
            )
        );

        return Inertia::render(AeasHelpers::AEAS_PACKAGE . '/Roles/EditRole', [
            'pbrole' => $role,
        ]);
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
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:20', Rule::unique($this->table)->ignore($id)],
            'alias' => ['required', 'max:190'],
            /* 'status' => ['required'], */
        ]);
        $this->validationCheck($validator, $request);

        // Requests
        $name = $request['name'];
        $permissions = $request['permissions'];
        $alias = $request['alias'];

        $me = PbUser::find(Auth::user()->id);
        $optionalPermissions = [];
        if ($me->hasRole('super-admin')) {
            $optionalPermissions = PbPermission::whereIn('name', ['read roles', 'read configs', 'read permissions', 'read navigations'])->get()->modelKeys();
        }
        $adminOptionalPermissions = array_intersect($optionalPermissions, $permissions);
        // Process
        try {
            $role = PbRoles::findOrFail($id);
            $role->name = $name;
            $role->alias = $alias;
            if ($role->save()) {
                if ($role->name == 'super-admin') {
                    $permissions = PbPermission::all()->modelKeys();
                } elseif ($role->name == 'admin') {
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
                } elseif ($role->name == 'user') {
                    $permissions = PbPermission::whereIn('name', ['login'])->get()->modelKeys();
                }
                $role->syncPermissions($permissions);
            }

            return $this->redirectResponseCRUDSuccess($request, 'Permission updated successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'Permission could not be updated!');
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
            $role = PbRoles::findOrFail($id);
            $role->delete();

            return $this->redirectResponseCRUDSuccess($request, 'Permission deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'Permission could not be deleted!');
        }
    }
}
