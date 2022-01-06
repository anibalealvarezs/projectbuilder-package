<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\User;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use App\Http\Requests;

use App\Models\Team;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Session;

use Inertia\Response as InertiaResponse;

class PbUserController extends PbBuilderController
{
    function __construct(Request $request, $crud_perms = false)
    {
        // Vars Override
        $this->keys = [
            'level' => 'User'
        ];
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
        // Default values
        $this->defaults = [
            'lang' => 'es',
            'country' => 'ES'
        ];
        // Parent construct
        parent::__construct($request, true);
        // Middlewares
        $this->middleware(['is_'.$this->controllerVars->level->name.'_editable'])->only('edit', 'update');
        $this->middleware(['is_'.$this->controllerVars->level->name.'_deletable'])->only('destroy');
        $this->middleware(['is_'.$this->controllerVars->level->name.'_viewable'])->only('show');
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
        $this->required = [
            ...$this->required,
            ...['roles', 'email']
        ];

        $query = $this->controllerVars->level->modelPath::withPublicRelations();
        $currentUser = $this->controllerVars->level->modelPath::current();
        if (!$currentUser->hasRole('super-admin')) {
            $superAdmins = $this->controllerVars->level->modelPath::role('super-admin')->get()->modelKeys();
            $query = $query->whereNotIn('id', $superAdmins);
            if (!$currentUser->hasRole('admin')) {
                $admins = $this->controllerVars->level->modelPath::role('admin')->get()->modelKeys();
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
                'api',
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
     * @return InertiaResponse|JsonResponse
     */
    public function create(string $route = 'level'): InertiaResponse|JsonResponse
    {
        $this->allowed = [
            'create '.$this->controllerVars->level->names => 'create',
        ];

        $this->required = [
            ...$this->required,
            ...['roles', 'password', 'email']
        ];

        return $this->renderResponse($this->controllerVars->level->viewsPath.'Create'.$this->controllerVars->level->key);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->validationRules['email'] = ['required', 'max:50', 'email', Rule::unique($this->controllerVars->level->table)];
        $this->validationRules['password'] = ['required'];

        $validationRules2 = [
            'roles' => ['required'],
        ];

        // Validation
        if ($failed = $this->validateRequest([...$this->validationRules, ...$validationRules2], $request)) {
            return $failed;
        }

        $roles = $request->input('roles');
        $lang = $request->input('lang');
        $country = $request->input('country');

        // Process
        try {
            // Build model
            $model = new $this->controllerVars->level->modelPath();
            // Add requests
            $model = $this->processModelRequests($this->validationRules, $request, $this->replacers, $model);
            $model->language_id = $lang;
            $model->country_id = $country;
            if ($model->save()) {
                $model->current_team_id = $this->getDefaultTeamId($model);
                if ($model->save()) {
                    $me = PbUser::current();
                    if ($me->hasRole(['super-admin'])) {
                        if (in_array('super-admin', $roles)) {
                            // Add only super-admin
                            $roles = ['super-admin'];
                        } elseif (in_array('admin', $roles)) {
                            // Add admin, and change developer and api-user
                            $roles = [
                                ...['admin'],
                                ...array_intersect(['developer', 'api-user'], $roles)
                            ];
                        }
                    } elseif ($me->hasRole(['admin'])) {
                        // Remove super-admin/admin
                        $toExclude = ['super-admin', 'admin'];
                        $intersect = array_intersect($roles, $toExclude);
                        $roles = array_diff($roles, $intersect);
                    } else {
                        // Remove super-admin/admin/developer/api-user
                        $toExclude = ['super-admin', 'admin', 'developer', 'api-user'];
                        $intersect = array_intersect($roles, $toExclude);
                        $roles = array_diff($roles, $intersect);
                    }
                    $model->syncRoles($roles);
                }
            }

            return $this->redirectResponseCRUDSuccess($request, $this->controllerVars->level->key.' created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->controllerVars->level->key.' could not be created! '.$e->getMessage());
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
        if ((Auth::user()->id == $id) && !$this->request->is('api/*')) {
            return redirect(DIRECTORY_SEPARATOR.$this->controllerVars->level->name.'/profile');
        }

        $model = $this->controllerVars->level->modelPath::withPublicRelations()->find($id);

        return parent::show($id, $model);
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
        if (Auth::user()->id == $id) {
            return redirect(DIRECTORY_SEPARATOR.$this->controllerVars->level->name.'/profile');
        }

        $model = $this->controllerVars->level->modelPath::withPublicRelations()->find($id);

        $this->required = [
            ...$this->required,
            ...['roles', 'email']
        ];

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
        $this->validationRules['email'] = ['required', 'max:50', 'email', Rule::unique($this->controllerVars->level->table)->ignore($id)];

        $validationRules2 = [
            'roles' => ['required'],
        ];

        // Validation
        if ($failed = $this->validateRequest([...$this->validationRules, ...$validationRules2], $request)) {
            return $failed;
        }

        // Requests
        $roles = $request->input('roles');
        $lang = $request->input('lang');
        $country = $request->input('country');
        $password = $request->input('password');

        // Process
        try {
            // Build model
            $model = $this->controllerVars->level->modelPath::find($id);
            // Build requests
            $requests = $this->processModelRequests($this->validationRules, $request, $this->replacers);
            if ($password != "") {
                $requests['password'] = $password;
            }
            $requests['language_id'] = $lang;
            $requests['country_id'] = $country;
            if ($model->update($requests)) {
                $me = PbUser::current();
                if ($me->hasRole(['super-admin'])) {
                    if (($model->id == Auth::user()->id) || in_array('super-admin', $roles)) {
                        // Add only super-admin
                        $roles = ['super-admin'];
                    } elseif (in_array('admin', $roles)) {
                        // Add admin, and change developer and api-user
                        $roles = [
                            ...['admin'],
                            array_intersect(['developer', 'api-user'], $roles)
                        ];
                    }
                } elseif ($me->hasRole(['admin'])) {
                    if (($model->id == Auth::user()->id) || $model->hasRole(['admin'])) {
                        // Add admin, and change developer and api-user
                        $roles = [
                            ...['admin'],
                            ...array_intersect(['developer', 'api-user'], $roles)
                        ];
                    } else {
                        // Remove super-admin/admin
                        $toExclude = ['super-admin', 'admin'];
                        $intersect = array_intersect($roles, $toExclude);
                        $roles = array_diff($roles, $intersect);
                    }
                } else {
                    // Remove super-admin/admin/developer/api-user
                    $toExclude = ['super-admin', 'admin', 'developer', 'api-user'];
                    $intersect = array_intersect($roles, $toExclude);
                    $roles = array_diff($roles, $intersect);
                    if ($model->hasRole(['developer'])) {
                        // Keep developer
                        array_push($roles, 'developer');
                    }
                    if ($model->hasRole(['api-user'])) {
                        // Keep api-user
                        array_push($roles, 'api-user');
                    }
                }
                $model->syncRoles($roles);
            }

            return $this->redirectResponseCRUDSuccess($request, $this->controllerVars->level->key.' updated successfully!');
        } catch (Exception $e) {

            return $this->redirectResponseCRUDFail($request, $this->controllerVars->level->key.' could not be updated! '.$e->getMessage());
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
