<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Role;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use App\Http\Requests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\Rule;

use Auth;
use DB;

use Inertia\Response as InertiaResponse;

use Session;

class PbRoleController extends PbBuilderController
{
    protected Array $superAdminExclusivePermissions;

    protected Array $adminOptionalPermissions;

    public function __construct(Request $request, $crud_perms = false)
    {
        $this->varsObject([
            'keys' => [
                'level' => 'Role'
            ],
            'validationRules' => [
                'alias' => ['required', 'max:190'],
            ],
            'shares' => [
                'permissions',
            ],
            'showId' => false,
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
        string $route = 'level'): InertiaResponse|JsonResponse|RedirectResponse
    {
        $this->pushRequired(['name']);

        $query = $this->vars->level->modelPath::withPublicRelations()->whereNotIn('name', ['super-admin', 'developer', 'api-user']);

        if (!PbUser::current()->hasRole('super-admin')) {
            $query->whereNotIn('name', ['admin']);
        }

        return parent::index(
            $page,
            $perpage,
            $orderby,
            $field,
            $order,
            $this->buildPaginatedAndOrderedModel(
                $query,
                $page,
                $perpage,
                $orderby,
                $field,
                $order
            )
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
        $this->pushRequired(['name']);

        return parent::create($route);
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
            'name' => ['required', 'max:20', Rule::unique($this->vars->level->table)],
        ]);

        // Validation
        if ($failed = $this->validateRequest($this->vars->validationRules, $request)) {
            return $failed;
        }

        $permissions = $request->input('permissions');

        // Process
        try {
            // Build model
            $model = (new $this->vars->level->modelPath())->setLocale(app()->getLocale());
            // Add requests
            $model = $this->processModelRequests($this->vars->validationRules, $request, $this->vars->replacers, $model);
            // Add additional fields values
            $model->guard_name = 'admin';
            // Model save
            if (!$model->save()) {
                return $this->redirectResponseCRUDFail($request, 'create', "Error saving {$this->vars->level->name}");
            }
            // Permissions save
            $this->loadDefaultPermissions();
            $permissions =
                PbPermission::whereNotIn('id', $this->superAdminExclusivePermissions)
                    ->whereNotIn('id', $this->adminOptionalPermissions)
                    ->whereIn('id', $permissions)->get();
            $model->syncPermissions($permissions);

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
        $this->pushRequired(['name']);

        return parent::edit($id, $this->vars->level->modelPath::withPublicRelations()->whereNotIn('name', ['super-admin', 'developer', 'api-user'])->findOrFail($id));
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
            'name' => ['required', 'max:20', Rule::unique($this->vars->level->table)->ignore($id)],
        ]);

        // Validation
        if ($failed = $this->validateRequest($this->vars->validationRules, $request)) {
            return $failed;
        }

        $permissions = $request->input('permissions');

        $me = PbUser::current();

        // Process
        try {
            // Build model
            if (!$model = $this->vars->level->modelPath::find($id)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error finding {$this->vars->level->name}");
            }
            if ($this->isUnmodifiableModel($model)) {
                return $this->redirectResponseCRUDFail($request, 'update', "This {$this->vars->level->name} cannot be modified");
            }
            if (!$model->isEditableBy(Auth::user()->id)) {
                return $this->redirectResponseCRUDFail($request, 'update', "You don't have permission to edit this {$this->vars->level->name}");
            }
            $model->setLocale(app()->getLocale());
            // Build requests
            $requests = $this->processModelRequests($this->vars->validationRules, $request, $this->vars->replacers);
            // Model update
            if (!$model->update($requests)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error updating {$this->vars->level->name}");
            }
            // Update permissions
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

            return $this->redirectResponseCRUDSuccess($request, 'update');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'update', $e->getMessage());
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
                    'create roles',
                    'update roles',
                    'delete roles',
                    'create permissions',
                    'update permissions',
                    'delete permissions',
                    'create configs',
                    'update configs',
                    'delete configs',
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
