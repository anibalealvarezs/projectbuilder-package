<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Permission;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Helpers\ControllerTrait;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;
use Anibalealvarezs\Projectbuilder\Models\PbRoles;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

    use ControllerTrait;

    public function __construct() {
        $this->middleware(['role_or_permission:admin roles permissions']);
        $this->aeas = new AeasHelpers();
        $this->name = "permissions";
        $permissions = new PbPermission();
        $this->table = $permissions->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $permissions = PbPermission::all(); //Get all permissions

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
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:40'],
        ]);

        $name = $request['name'];
        $roles = $request['roles'];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $current = "";
            foreach ($errors->all() as $message) {
                $current = $message;
            }
            $request->session()->flash('flash.banner', $current);
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->back()->withInput();
        } else {

            try {
                $permission = new PbPermission();
                $permission->name = $name;
                $permission->guard_name = 'admin';
                if ($permission->save()) {
                    $adminRoles = PbRoles::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                    $p = PbPermission::findOrFail($permission->id);
                    $p->syncRoles(array_merge($roles,$adminRoles));
                }

                $request->session()->flash('flash.banner', 'Permission Created Successfully!');
                $request->session()->flash('flash.bannerStyle', 'success');

                return redirect()->route($this->name.'.index');
            } catch (Exception $e) {
                $request->session()->flash('flash.banner', 'Permission could not be created!');
                $request->session()->flash('flash.bannerStyle', 'danger');

                return redirect()->back()->withInput();
            }
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

        return redirect()->route($this->name.'.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return InertiaResponse
     */
    public function edit(int $id): InertiaResponse
    {
        $permission = PbPermission::findOrFail($id);
        $currentRoles = $permission->roles->modelKeys();

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
            'currentroles' => $currentRoles
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:40'],
        ]);

        $name = $request['name'];
        $roles = $request['roles'];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $current = "";
            foreach ($errors->all() as $message) {
                $current = $message;
            }
            $request->session()->flash('flash.banner', $current);
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->back()->withInput();
        } else {

            $permission = PbPermission::findOrFail($id);
            try {
                $permission->name = $name;
                if ($permission->save()) {
                    $p = PbPermission::findOrFail($permission->id);
                    if (in_array($permission->name, ['admin roles permissions', 'crud super-admin'])) {
                        $superAdminRoles = PbRoles::whereIn('name', ['super-admin'])->get()->modelKeys();
                        $p->syncRoles($superAdminRoles);
                    } elseif(in_array($permission->name, ['manage app'])) {
                        $adminRoles = PbRoles::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                        $p->syncRoles($adminRoles);
                    } elseif(in_array($permission->name, ['login'])) {
                        $adminRoles = PbRoles::all()->modelKeys();
                        $p->syncRoles($adminRoles);
                    } else {
                        $adminRoles = PbRoles::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                        $p->syncRoles(array_merge($roles,$adminRoles));
                    }
                }

                $request->session()->flash('flash.banner', 'Permission Created Successfully!');
                $request->session()->flash('flash.bannerStyle', 'success');

                return redirect()->route($this->name.'.index');
            } catch (Exception $e) {
                $request->session()->flash('flash.banner', 'Permission could not be updated!');
                $request->session()->flash('flash.bannerStyle', 'danger');

                return redirect()->back()->withInput();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $permission = PbPermission::findOrFail($id);

        //Make it impossible to delete these specific permissions
        if (in_array($permission->name, ['admin roles permissions', 'manage app', 'crud super-admin'])) {
            $request->session()->flash('flash.banner', 'This permission can not be deleted!');
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->route($this->name.'.index');
        }

        try {
            $permission->delete();

            $request->session()->flash('flash.banner', 'Permission deleted successfully!');
            $request->session()->flash('flash.bannerStyle', 'success');

            return redirect()->route($this->name.'.index')->withInput();
        } catch (Exception $e) {
            $request->session()->flash('flash.banner', 'Permission could not be deleted!');
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->back()->withInput();
        }
    }
}
