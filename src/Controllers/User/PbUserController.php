<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\User;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Models\PbUser;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Session;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PbUserController extends Controller
{
    protected $aeas;
    protected $name;
    protected $table;

    use PbControllerTrait;

    function __construct()
    {
        // Middlewares
        $this->middleware(['role_or_permission:read users']);
        $this->middleware(['is_user_viewable'])->only('show');
        $this->middleware(['role_or_permission:create users'])->only('create', 'store');
        $this->middleware(['role_or_permission:update users', 'is_user_editable'])->only('edit', 'update');
        $this->middleware(['role_or_permission:delete users', 'is_user_deletable'])->only('destroy');
        // Variables
        $this->aeas = new AeasHelpers();
        $this->name = "users";
        $this->table = (new PbUser())->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $usersQuery = PbUser::with('country', 'city', 'lang', 'roles');
        $currentUser = PbUser::find(Auth::user()->id);
        if (!$currentUser->hasRole('super-admin')) {
            $superAdmins = PbUser::role('super-admin')->get()->modelKeys();
            $usersQuery = $usersQuery->whereNotIn('id', $superAdmins);
            if (!$currentUser->hasRole('admin')) {
                $admins = PbUser::role('admin')->get()->modelKeys();
                $usersQuery = $usersQuery->whereNotIn('id', $admins);
            }
        }
        $users = $usersQuery->get();
        $filtered = $users->map(function ($user) {
            return $user->only([
                'id',
                'name',
                'email',
                'last_session',
                'created_at',
                'country',
                'city',
                'lang',
                'roles',
                'crud',
            ]);
        })->sortByDesc(['name', 'email']);

        $filtered = $this->aeas->setCollectionAttributeDatetimeFormat(
            $filtered,
            ['created_at', 'last_session'],
            "custom",
            "d/m/y"
        );

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'languages',
                    'countries',
                    'roles',
                    'me',
                ]),
                Shares::allowed([
                    'create users' => 'create',
                    'update users' => 'update',
                    'delete users' => 'delete',
                ]),
            )
        );

        return Inertia::render($this->aeas->package . '/Users/Users', [
            'pbusers' => $filtered,
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
                    'languages',
                    'countries',
                    'roles',
                ]),
                Shares::allowed([
                    'create users' => 'create',
                ]),
            )
        );

        return Inertia::render($this->aeas->package . '/Users/CreateUser');
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
            'name' => ['required', 'max:190'],
            'roles' => ['required'],
            'email' => ['required', 'max:50', 'email', Rule::unique($this->table)],
            'password' => ['required'],
        ]);
        $this->validationCheck($validator, $request);

        // Requests
        $roles = $request['roles'];
        $lang = $request->input('lang');
        $country = $request->input('country');

        // Process
        try {
            if ($user = PbUser::create($request->all())) {
                $user->current_team_id = $this->getDefaultTeamId($user);
                $user->language_id = $lang;
                $user->country_id = $country;
                if ($user->save()) {
                    $me = PbUser::find(Auth::user()->id);
                    if ($me->hasRole(['super-admin'])) {
                        // Add only super-admin/admin
                        if ($user->id == Auth::user()->id) {
                            $roles = ['super-admin'];
                        } elseif (in_array('admin', $roles)) {
                            $roles = ['admin'];
                        }
                    } else {
                        // Remove super-admin/admin
                        $toExclude = ['super-admin', 'admin'];
                        $intersect = array_intersect($roles, $toExclude);
                        $roles = array_diff($roles, $intersect);
                    }
                    $user->syncRoles($roles);
                }
            }

            return $this->redirectResponseCRUDSuccess($request, 'User created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'User could not be created!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        if (Auth::user()->id == $id) {
            return redirect('/user/profile');
        }

        $user = PbUser::with('country', 'city', 'lang', 'roles')->find($id);

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
            )
        );

        return Inertia::render($this->aeas->package . '/Users/ShowUser', [
            'pbuser' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return mixed
     */
    public function edit(Request $request, int $id)
    {
        if (Auth::user()->id == $id) {
            return redirect('/user/profile');
        }

        $user = PbUser::with('country', 'city', 'lang', 'roles')->find($id);

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'languages',
                    'countries',
                    'roles',
                ]),
            )
        );

        return Inertia::render($this->aeas->package . '/Users/EditUser', [
            'pbuser' => $user
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
            'name' => ['required', 'max:190'],
            'roles' => ['required'],
            'email' => ['required', 'max:50', 'email', Rule::unique($this->table)->ignore($id)],
        ]);
        $this->validationCheck($validator, $request);

        // Requests
        $roles = $request['roles'];

        // Process
        try {
            $user = PbUser::find($id);
            $me = PbUser::find(Auth::user()->id);
            if ($request->input('password') == "") {
                unset($user->password);
            }
            $user->language_id = $request->input('lang');
            $user->country_id = $request->input('country');
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            if ($user->save()) {
                if ($me->hasRole(['super-admin'])) {
                    // Add only super-admin/admin
                    if ($user->id == Auth::user()->id) {
                        $roles = ['super-admin'];
                    } elseif (in_array('admin', $roles)) {
                        $roles = ['admin'];
                    }
                } else {
                    if ($user->hasRole(['admin'])) {
                        $roles = ['admin'];
                    } else {
                        // Remove super-admin/admin
                        $toExclude = ['super-admin', 'admin'];
                        $intersect = array_intersect($roles, $toExclude);
                        $roles = array_diff($roles, $intersect);
                    }
                }
                $user->syncRoles($roles);
            }

            return $this->redirectResponseCRUDSuccess($request, 'User updated successfully!');
        } catch (Exception $e) {

            return $this->redirectResponseCRUDFail($request, 'User could not be updated!');
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
            $user = PbUser::find($id);
            $user->delete();

            return $this->redirectResponseCRUDSuccess($request, 'User deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'User could not be deleted!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param null $user
     * @return mixed
     */
    protected function getDefaultTeamId($user = null)
    {
        $team = Team::where('name', 'User')->first();
        if ($user && $user->hasRole('admin')) {
            $team = Team::where('name', 'Admin')->first();
        }

        return $team->id;
    }
}
