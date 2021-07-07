<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\User;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;

use App\Http\Requests;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Session;

use Inertia\Inertia;
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
        ${$this->names} = $query->get();
        $filtered = ${$this->names}->map(function ($q) {
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

        $filtered = PbHelpers::setCollectionAttributeDatetimeFormat(
            $filtered,
            ['created_at', 'last_session'],
            "custom",
            "d/m/y"
        );

        $shares = [
            'languages',
            'countries',
            'roles',
            'me',
        ];

        return parent::index($filtered, $shares);
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
            'languages',
            'countries',
            'roles',
        ];

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list($shares),
                Shares::allowed([
                    'create '.$this->names => 'create',
                ]),
            )
        );

        return Inertia::render($this->viewsPath.'Create'.$this->key);
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
            'name' => ['required', 'max:190'],
            'email' => ['required', 'max:50', 'email', Rule::unique($this->table)],
            'password' => ['required'],
        ];

        $validationRules2 = [
            'roles' => ['required'],
        ];

        $this->storeValidation = array_merge($this->storeValidation, array_merge($validationRules, $validationRules2));

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

        $roles = $request->input('roles');
        $lang = $request->input('lang');
        $country = $request->input('country');

        // Process
        try {
            ${$this->name} = new $this->modelPath();
            foreach($keys as $key) {
                ${$this->name}->$key = ${$key};
            }
            ${$this->name}->language_id = $lang;
            ${$this->name}->country_id = $country;
            if (${$this->name}->save()) {
                ${$this->name}->current_team_id = $this->getDefaultTeamId(${$this->name});
                if (${$this->name}->save()) {
                    $me = $this->modelPath::find(Auth::user()->id);
                    if ($me->hasRole(['super-admin'])) {
                        // Add only super-admin/admin
                        if (${$this->name}->id == Auth::user()->id) {
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
                    ${$this->name}->syncRoles($roles);
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
     * @param null $element
     * @param int $id
     * @param array $shares
     * @return InertiaResponse
     */
    public function show(int $id, $element = null, array $shares = []): InertiaResponse
    {
        if (Auth::user()->id == $id) {
            return redirect('/'.${$this->name}.'/profile');
        }

        ${$this->name} = $this->modelPath::with('country', 'city', 'lang', 'roles')->find($id);

        return parent::show($id, ${$this->name});
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param null $element
     * @param array $shares
     * @return InertiaResponse
     */
    public function edit(int $id, $element = null, array $shares = []): InertiaResponse
    {
        if (Auth::user()->id == $id) {
            return redirect('/'.${$this->name}.'/profile');
        }

        ${$this->name} = $this->modelPath::with('country', 'city', 'lang', 'roles')->find($id);

        $shares = [
            'languages',
            'countries',
            'roles',
        ];

        return parent::edit($id, ${$this->name}, $shares);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @param array $validationRules
     * @param array $replacers
     * @return void
     */
    public function update(Request $request, int $id, array $validationRules = [], array $replacers = [])
    {
        $validationRules = [
            'name' => ['required', 'max:190'],
            'email' => ['required', 'max:50', 'email', Rule::unique($this->table)->ignore($id)],
        ];

        $validationRules2 = [
            'roles' => ['required'],
        ];

        $this->storeValidation = array_merge($this->storeValidation, array_merge($validationRules, $validationRules2));

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

        // Requests
        $roles = $request->input('roles');
        $lang = $request->input('lang');
        $country = $request->input('country');
        $password = $request->input('password');

        // Process
        try {
            ${$this->name} = $this->modelPath::find($id);
            $requests = [];
            foreach($keys as $key) {
                $requests[$key] = ${$key};
            }
            if ($password != "") {
                $requests['password'] = $password;
            }
            $requests['language_id'] = $lang;
            $requests['country_id'] = $country;
            if (${$this->name}->update($requests)) {
                $me = $this->modelPath::find(Auth::user()->id);
                if ($me->hasRole(['super-admin'])) {
                    // Add only super-admin/admin
                    if (${$this->name}->id == Auth::user()->id) {
                        $roles = ['super-admin'];
                    } elseif (in_array('admin', $roles)) {
                        $roles = ['admin'];
                    }
                } else {
                    if (${$this->name}->hasRole(['admin'])) {
                        $roles = ['admin'];
                    } else {
                        // Remove super-admin/admin
                        $toExclude = ['super-admin', 'admin'];
                        $intersect = array_intersect($roles, $toExclude);
                        $roles = array_diff($roles, $intersect);
                    }
                }
                ${$this->name}->syncRoles($roles);
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
