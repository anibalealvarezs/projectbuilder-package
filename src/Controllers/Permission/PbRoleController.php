<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Permission;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Helpers\ControllerTrait;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;
use Anibalealvarezs\Projectbuilder\Models\PbRoles;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Auth;
use DB;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

use Session;

class PbRoleController extends Controller
{
    protected $aeas;
    protected $name;
    protected $table;

    use ControllerTrait;

    public function __construct() {
        $this->middleware(['role_or_permission:admin roles permissions']);
        $this->aeas = new AeasHelpers();
        $this->name = "roles";
        $roles = new PbRoles();
        $this->table = $roles->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $roles = PbRoles::all(); //Get all permissions

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'permissions',
                ]),
            )
        );

        return Inertia::render($this->aeas->package . '/Roles/Roles', [
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

        return Inertia::render($this->aeas->package . '/Roles/CreateRole');
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
            'name' => ['required', 'max:20', Rule::unique($this->table)],
            'alias' => ['required', 'max:190'],
            /* 'status' => ['required'], */
        ]);

        $name = $request['name'];
        $permissions = $request['permissions'];
        $alias = $request['alias'];

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
                $role = new PbRoles();
                $role->name = $name;
                $role->alias = $alias;
                $role->guard_name = 'admin';
                // $role->alias = $request['alias'];
                if ($role->save()) {
                    $r = PbRoles::findOrFail($role->id);
                    $r->syncPermissions($permissions);
                }

                $request->session()->flash('flash.banner', 'Role Created Successfully!');
                $request->session()->flash('flash.bannerStyle', 'success');

                return redirect()->route($this->name.'.index');
            } catch (Exception $e) {
                $request->session()->flash('flash.banner', 'Role could not be created!');
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
        $role = PbRoles::findOrFail($id);
        $currentPermissions = $role->permissions->modelKeys();

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'permissions',
                ]),
            )
        );

        return Inertia::render($this->aeas->package . '/Roles/EditRole', [
            'pbrole' => $role,
            'currentpermissions' => $currentPermissions
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
            'name' => ['required', 'max:20', Rule::unique($this->table)->ignore($id)],
            'alias' => ['required', 'max:190'],
            /* 'status' => ['required'], */
        ]);

        $name = $request['name'];
        $permissions = $request['permissions'];
        $alias = $request['alias'];

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

            $role = PbRoles::findOrFail($id);
            try {
                $role->name = $name;
                $role->alias = $alias;
                if ($role->save()) {
                    $r = PbRoles::findOrFail($role->id);
                    if ($role->name == 'super-admin') {
                        $r->syncPermissions(PbPermission::all()->modelKeys());
                    } elseif ($role->name == 'admin') {
                        $r->syncPermissions(PbPermission::whereNotIn('name', ['admin roles permissions', 'crud super-admin'])->get()->modelKeys());
                    } elseif ($role->name == 'user') {
                        $r->syncPermissions(PbPermission::whereIn('name', ['login'])->get()->modelKeys());
                    } else {
                        $r->syncPermissions($permissions);
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
        $role = PbRoles::findOrFail($id);

        try {
            $role->delete();

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
