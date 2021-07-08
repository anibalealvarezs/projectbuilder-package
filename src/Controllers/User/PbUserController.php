<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\User;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use App\Http\Requests;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Session;

use Inertia\Response as InertiaResponse;

class PbUserController extends PbBuilderController
{
    function __construct($crud_perms = false)
    {
        // Vars Override
        $this->key = 'User';
        // Parent construct
        parent::__construct(true);
        // Middlewares
        $this->middleware(['is_'.$this->name.'_editable'])->only('edit', 'update');
        $this->middleware(['is_'.$this->name.'_deletable'])->only('destroy');
        $this->middleware(['is_'.$this->name.'_viewable'])->only('show');
        // Validation Rules
        $this->validationRules = [
            'name' => ['required', 'max:190'],
        ];
        // Model fields name replacing
        $this->replacers = [
            'permission' => 'permission_id'
        ];
        // Additional variables to share
        $this->shares = [
            'languages',
            'countries',
            'roles',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse
     */
    public function index($element = null, bool $multiple = false, string $route = 'level'): InertiaResponse
    {
        $query = $this->modelPath::with('country', 'city', 'lang', 'roles');
        $currentUser = $this->modelPath::find(Auth::user()->id);
        if (!$currentUser->hasRole('super-admin')) {
            $superAdmins = $this->modelPath::role('super-admin')->get()->modelKeys();
            $query = $query->whereNotIn('id', $superAdmins);
            if (!$currentUser->hasRole('admin')) {
                $admins = $this->modelPath::role('admin')->get()->modelKeys();
                $query = $query->whereNotIn('id', $admins);
            }
        }
        $model = $query->get();
        $filtered = $model->map(function ($q) {
            return $q->only([
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

        $filtered = $this->helper::setCollectionAttributeDatetimeFormat(
            $filtered,
            ['created_at', 'last_session'],
            "custom",
            "d/m/y"
        );

        $this->shares[] = 'me';

        return parent::index($filtered);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $route
     * @return InertiaResponse
     */
    public function create(string $route = 'level'): InertiaResponse
    {
        $this->allowed = [
            'create '.$this->names => 'create',
        ];

        return $this->renderView($this->viewsPath.'Create'.$this->key);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->validationRules['email'] = ['required', 'max:50', 'email', Rule::unique($this->table)];
        $this->validationRules['password'] = ['required'];

        $validationRules2 = [
            'roles' => ['required'],
        ];

        // Validation
        $this->validateRequest('store', array_merge($this->validationRules, $validationRules2), $request);

        $roles = $request->input('roles');
        $lang = $request->input('lang');
        $country = $request->input('country');

        // Process
        try {
            // Build model
            $model = new $this->modelPath();
            // Add requests
            $model = $this->processModelRequests($this->validationRules, $request, $this->replacers, $model);
            $model->language_id = $lang;
            $model->country_id = $country;
            if ($model->save()) {
                $model->current_team_id = $this->getDefaultTeamId($model);
                if ($model->save()) {
                    $me = PbUser::find(Auth::user()->id);
                    if ($me->hasRole(['super-admin'])) {
                        // Add only super-admin/admin
                        if ($model->id == Auth::user()->id) {
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
                    $model->syncRoles($roles);
                }
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
        if (Auth::user()->id == $id) {
            return redirect('/'.${$this->name}.'/profile');
        }

        $model = $this->modelPath::with('country', 'city', 'lang', 'roles')->find($id);

        return parent::show($id, $model);
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
        if (Auth::user()->id == $id) {
            return redirect('/'.${$this->name}.'/profile');
        }

        $model = $this->modelPath::with('country', 'city', 'lang', 'roles')->find($id);

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
        $this->validationRules['email'] = ['required', 'max:50', 'email', Rule::unique($this->table)->ignore($id)];

        $validationRules2 = [
            'roles' => ['required'],
        ];

        // Validation
        $this->validateRequest('update', array_merge($this->validationRules, $validationRules2), $request);

        // Requests
        $roles = $request->input('roles');
        $lang = $request->input('lang');
        $country = $request->input('country');
        $password = $request->input('password');

        // Process
        try {
            // Build model
            $model = $this->modelPath::find($id);
            // Build requests
            $requests = $this->processModelRequests($this->validationRules, $request, $this->replacers);
            if ($password != "") {
                $requests['password'] = $password;
            }
            $requests['language_id'] = $lang;
            $requests['country_id'] = $country;
            if ($model->update($requests)) {
                $me = PbUser::find(Auth::user()->id);
                if ($me->hasRole(['super-admin'])) {
                    // Add only super-admin/admin
                    if ($model->id == Auth::user()->id) {
                        $roles = ['super-admin'];
                    } elseif (in_array('admin', $roles)) {
                        $roles = ['admin'];
                    }
                } else {
                    if ($model->hasRole(['admin'])) {
                        $roles = ['admin'];
                    } else {
                        // Remove super-admin/admin
                        $toExclude = ['super-admin', 'admin'];
                        $intersect = array_intersect($roles, $toExclude);
                        $roles = array_diff($roles, $intersect);
                    }
                }
                $model->syncRoles($roles);
            }

            return $this->redirectResponseCRUDSuccess($request, $this->key.' updated successfully!');
        } catch (Exception $e) {

            return $this->redirectResponseCRUDFail($request, $this->key.' could not be updated!');
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
