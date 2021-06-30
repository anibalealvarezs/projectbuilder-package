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

use Auth;
use DB;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

use Session;

class PbPermissionController extends Controller
{
    protected $aeas;
    protected $name;
    protected $table;

    use PbControllerTrait;

    public function __construct()
    {
        // Middlewares
        $this->middleware(['role_or_permission:admin roles permissions']);
        // Variables
        $this->aeas = new AeasHelpers();
        $this->name = "permissions";
        $this->table = (new PbPermission())->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $user = PbUser::find(Auth::user()->id);
        $toExclude = ['crud super-admin'];
        if (!$user->hasRole('super-admin')) {
            $toExclude = array_merge($toExclude, ['admin roles permissions', 'manage app']);
            if (!$user->hasRole('admin')) {
                $toExclude = array_merge($toExclude, ['login', 'create users', 'update users', 'delete users']);
            }
        }
        $permissions = PbPermission::with('roles')->whereNotIn('name', $toExclude)->get(); //Get all permissions

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'roles',
                ]),
            )
        );

        return Inertia::render($this->aeas->package . '/Permissions/Permissions', [
            'pbpermissions' => $permissions,
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
                    'roles',
                ]),
            )
        );

        return Inertia::render($this->aeas->package . '/Permissions/CreatePermission');
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
            'name' => ['required', 'max:40'],
            'alias' => ['required', 'max:190'],
        ]);
        $this->validationCheck($validator, $request);

        // Requests
        $name = $request['name'];
        $roles = $request['roles'];
        $alias = $request['alias'];

        // Process
        try {
            $permission = new PbPermission();
            $permission->name = $name;
            $permission->alias = $alias;
            $permission->guard_name = 'admin';
            if ($permission->save()) {
                $adminRoles = PbRoles::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                $permission->syncRoles(
                    array_merge(
                        (is_array($roles) ? $roles : [$roles]),
                        (is_array($adminRoles) ? $adminRoles : [$adminRoles])
                    )
                );
            }

            return $this->redirectResponseCRUDSuccess($request, 'Permission created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'Permission could not be created!');
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
        $permission = PbPermission::with('roles')->findOrFail($id);

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'roles',
                ]),
            )
        );

        return Inertia::render($this->aeas->package . '/Permissions/EditPermission', [
            'pbpermission' => $permission,
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
            'name' => ['required', 'max:40'],
            'alias' => ['required', 'max:190'],
        ]);
        $this->validationCheck($validator, $request);

        // Requests
        $name = $request['name'];
        $roles = $request['roles'];
        $alias = $request['alias'];

        // Process
        try {
            $permission = PbPermission::findOrFail($id);
            $permission->name = $name;
            $permission->alias = $alias;
            if ($permission->save()) {
                if (in_array($permission->name, ['crud super-admin'])) {
                    $superAdminRoles = PbRoles::whereIn('name', ['super-admin'])->get()->modelKeys();
                    $permission->syncRoles($superAdminRoles);
                } elseif (in_array($permission->name, ['manage app', 'admin roles permissions'])) {
                    $adminRoles = PbRoles::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                    $permission->syncRoles($adminRoles);
                } elseif (in_array($permission->name, ['login'])) {
                    $adminRoles = PbRoles::all()->modelKeys();
                    $permission->syncRoles($adminRoles);
                } else {
                    $adminRoles = PbRoles::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                    $permission->syncRoles(
                        array_merge(
                            (is_array($roles) ? $roles : [$roles]),
                            (is_array($adminRoles) ? $adminRoles : [$adminRoles])
                        )
                    );
                }
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
            $permission = PbPermission::findOrFail($id);
            //Make it impossible to delete these specific permissions
            if (in_array($permission->name, ['admin roles permissions', 'manage app', 'crud super-admin'])) {
                $request->session()->flash('flash.banner', 'This permission can not be deleted!');
                $request->session()->flash('flash.bannerStyle', 'danger');

                return redirect()->route($this->name . '.index');
            }
            $permission->delete();

            return $this->redirectResponseCRUDSuccess($request, 'Permission deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'Permission could not be deleted!');
        }
    }
}
