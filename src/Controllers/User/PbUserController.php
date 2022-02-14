<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\User;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Anibalealvarezs\Projectbuilder\Utilities\PbDebugbar;
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
        $this->varsObject([
            'keys' => [
                'level' => 'User'
            ],
            'validationRules' => [
                'name' => ['required', 'max:190'],
            ],
            'replacers' => [
                'permission' => 'permission_id'
            ],
            'shares' => [
                'languages',
                'countries',
                'roles',
            ],
            'defaults' => [
                'lang' => 'es',
                'country' => 'ES'
            ],
        ]);
        // Parent construct
        parent::__construct($request, true);
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $page
     * @param int $perpage
     * @param string|null $orderby
     * @param string $field
     * @param string $order
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse|RedirectResponse
     */
    public function index(
        int $page = 1,
        int $perpage = 0,
        string $orderby = null,
        string $field = 'id',
        string $order = 'asc',
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): InertiaResponse|JsonResponse|RedirectResponse
    {
        $this->pushRequired(['roles', 'email']);

        $this->vars->shares[] = 'me';
        $this->vars->config = $this->vars->level->modelPath::getCrudConfig(true);

        $model = $this->buildPaginatedAndOrderedModel(
            $this->vars->level->modelPath::withPublicRelations()->removeAdmins(),
            $page,
            $perpage,
            $orderby,
            $field,
            $order,
            ['name' => 'asc', 'email' => 'asc']
        );

        return parent::index(
            $page,
            $perpage,
            $orderby,
            $field,
            $order,
            $model,
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $route
     * @return InertiaResponse|JsonResponse
     */
    public function create(string $route = 'level'): InertiaResponse|JsonResponse
    {
        $this->vars->allowed = [
            'create '.$this->vars->level->names => 'create',
        ];

        $this->pushRequired(['roles', 'password', 'email']);

        return $this->renderResponse($this->buildRouteString($route, 'create'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse|null
     */
    public function store(Request $request): Redirector|RedirectResponse|Application|null
    {
        $this->pushValidationRules([
            'password' => ['required'],
            'email' => ['required', 'email', 'max:50', Rule::unique($this->vars->level->table)],
        ]);

        // Validation
        if ($failed = $this->validateRequest([...$this->vars->validationRules, ...['roles' => ['required']]], $request)) {
            return $failed;
        }

        $roles = $request->input('roles');
        $lang = $request->input('lang');
        $country = $request->input('country');

        // Process
        try {
            // Build model
            $model = (new $this->vars->level->modelPath())->setLocale(app()->getLocale());
            // Add requests
            $model = $this->processModelRequests($this->vars->validationRules, $request, $this->vars->replacers, $model);
            $model->language_id = $lang;
            $model->country_id = $country;
            // Model save
            if (!$model->save()) {
                return $this->redirectResponseCRUDFail($request, 'create', "Error saving {$this->vars->level->name}");
            }
            // Team assigning
            $model->current_team_id = $this->getDefaultTeamId($model);
            if (!$model->save()) {
                return $this->redirectResponseCRUDFail($request, 'create', "Error updating team for {$this->vars->level->name}");
            }
            // Roles assigning
            $me = app(PbCurrentUser::class);
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

            return $this->redirectResponseCRUDSuccess($request, 'create');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'create', $e->getMessage());
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
        if ((Auth::user()->id == $id) && !isApi($this->vars->request)) {
            return redirect(DIRECTORY_SEPARATOR.$this->vars->level->name.'/profile');
        }

        return parent::show($id, $this->vars->level->modelPath::withPublicRelations()->find($id));
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
        if ((Auth::user()->id == $id) && !isApi($this->vars->request)) {
            return redirect(DIRECTORY_SEPARATOR.$this->vars->level->name.'/profile');
        }

        $this->pushRequired(['roles', 'email']);

        return parent::edit($id, $this->vars->level->modelPath::withPublicRelations()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Application|Redirector|RedirectResponse|null
     */
    public function update(Request $request, int $id): Redirector|RedirectResponse|Application|null
    {
        $this->pushValidationRules([
            'email' => ['required', 'max:50', 'email', Rule::unique($this->vars->level->table)->ignore($id)],
        ]);

        // Validation
        if ($failed = $this->validateRequest([...$this->vars->validationRules, ...['roles' => ['required']]], $request)) {
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
            if (!$model = $this->vars->level->modelPath::find($id)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error finding {$this->vars->level->name} with id $id");
            }
            if ($this->isUnmodifiableModel($model)) {
                return $this->redirectResponseCRUDFail(request(), 'update', "This {$this->vars->level->name} cannot be modified");
            }
            if (!$model->isEditableBy(Auth::user()->id)) {
                return $this->redirectResponseCRUDFail($request, 'update', "You don't have permission to edit this {$this->vars->level->name}");
            }
            $model->setLocale(app()->getLocale());
            // Build requests
            $requests = $this->processModelRequests($this->vars->validationRules, $request, $this->vars->replacers);
            if ($password != "") {
                $requests['password'] = $password;
            }
            $requests['language_id'] = $lang;
            $requests['country_id'] = $country;
            // Model update
            if (!$model->update($requests)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error updating {$this->vars->level->name}");
            }
            // Sync roles
            $me = app(PbCurrentUser::class);
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

            return $this->redirectResponseCRUDSuccess($request, 'update');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'update', $e->getMessage());
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
