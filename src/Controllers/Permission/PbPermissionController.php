<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Permission;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Anibalealvarezs\Projectbuilder\Models\PbRole;
use Anibalealvarezs\Projectbuilder\Utilities\PbCache;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

use App\Http\Requests;

use Auth;
use DB;
use ReflectionException;
use Session;

use Inertia\Response as InertiaResponse;

class PbPermissionController extends PbBuilderController
{
    public function __construct(Request $request, $crud_perms = false)
    {
        $this->varsObject([
            'keys' => [
                'level' => 'Permission'
            ],
            'validationRules' => [
                'name' => ['required', 'max:40'],
                'alias' => ['required', 'max:190'],
            ],
            'shares' => [
                'roles',
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
     * @throws ReflectionException
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
        $this->startController(getClassName(__CLASS__));

        // Set cache/methods arguments
        $this->initArgs([
            'class' => 'model_controller',
            'pagination' => ['page' => $page, 'perpage' => $perpage, 'orderby' => $orderby, 'field' => $field, 'order' => $order],
            'byRoles' => true,
        ]);

        // Load model config
        $this->measuredRun(return: $this->vars->config, name: getClassName(__CLASS__) . ' - model config load', args: [
            'closure' => fn() => $this->vars->level->modelPath::getCrudConfig(true),
            'modelFunction' => 'getCrudConfig',
        ]);

        // Get models list
        $this->measuredRun(return: $model, name: getClassName(__CLASS__) . ' - models list build', args: [
            'closure' => function() {
                $toExclude = ['crud super-admin'];
                if (!app(PbCurrentUser::class)->hasRole('super-admin')) {
                    $toExclude = [
                        ...$toExclude,
                        ...[
                            'admin roles permissions',
                            'manage app',
                            'crud super-admin',
                            'config builder',
                            'developer options',
                            'read loggers',
                            'delete loggers',
                            'config loggers',
                            'api access',
                        ]
                    ];
                    if (!app(PbCurrentUser::class)->hasRole('admin')) {
                        $toExclude = [
                            ...$toExclude,
                            ...['login', 'create users', 'update users', 'delete users']
                        ];
                    }
                }
                return $this->buildPaginatedAndOrderedModel(
                    query: $this->vars->level->modelPath::whereNotIn('name', $toExclude)->withPublicRelations(),
                );
            },
            'modelFunction' => 'getList',
        ]);

        $this->stopController(getClassName(__CLASS__));

        return parent::index(
            element: $model,
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse|null
     */
    public function store(Request $request): Redirector|RedirectResponse|Application|null
    {
        // Validation
        if ($failed = $this->validateRequest($this->vars->validationRules, $request)) {
            return $failed;
        }

        $roles = $request->input('roles');

        // Process
        try {
            // Build model
            $model = resolve($this->vars->level->modelPath)->setLocale(app()->getLocale());
            // Add requests
            $model = $this->processModelRequests(
                validationRules: $this->vars->validationRules,
                request: $request,
                replacers: $this->vars->replacers,
                model: $model
            );
            // Add additional fields values
            $model->guard_name = 'admin';
            // Check if module relation exists
            $model = $this->checkModuleRelation($model);
            // Model save
            if (!$model->save()) {
                return $this->redirectResponseCRUDFail($request, 'create', "Error saving {$this->vars->level->name}");
            }

            PbCache::clearIndex(
                package: $this->vars->helper->package,
                models: $this->vars->level->names,
            );

            // Add roles
            $adminRoles = PbRole::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
            $model->syncRoles(
                [
                    ...($roles && is_array($roles) ? $roles : ($roles ? [$roles] : [])),
                    ...($adminRoles && is_array($adminRoles) ? $adminRoles : ($adminRoles ? [$adminRoles] : []))
                ]
            );

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
     * @throws ReflectionException
     */
    public function show(
        int $id,
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): Application|RedirectResponse|Redirector|InertiaResponse|JsonResponse {

        return $this->edit($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return RedirectResponse|InertiaResponse|JsonResponse
     * @throws ReflectionException
     */
    public function edit(
        int $id,
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): RedirectResponse|InertiaResponse|JsonResponse {

        $this->startController(getClassName(__CLASS__));

        // Set cache/methods arguments
        $this->initArgs([
            'class' => 'model_controller',
            'function' => 'edit',
            'model' => $this->vars->level->name,
            'byRoles' => true,
        ]);

        $this->measuredRun(return: $model, name: getClassName(__CLASS__) . ' - model find', args: [
            'closure' => fn() => $this->vars->level->modelPath::withPublicRelations()->find($id),
            'modelFunction' => 'find',
            'modelId' => $id,
        ]);

        if (!$model) {
            return $this->redirectResponseCRUDFail(request(), 'edit', "Error finding {$this->vars->level->name}");
        }

        $this->stopController(getClassName(__CLASS__));

        return parent::edit($id, $model);
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
        // Validation
        if ($failed = $this->validateRequest($this->vars->validationRules, $request)) {
            return $failed;
        }

        $roles = $request->input('roles');

        // Set cache/methods arguments
        $this->initArgs([
            'function' => 'update',
        ]);

        // Process
        try {
            // Build model
            if (!$model = $this->vars->level->modelPath::find($id)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error finding {$this->vars->level->name} with id $id");
            }
            // Push Unmodifiable Permissions
            $model = $this->pushUnmodifiablePermissions($model);
            // Check Permissions
            $this->checkPermissions($model);
            // Set Locale
            $model->setLocale(app()->getLocale());
            // Build requests
            $requests = $this->processModelRequests(
                validationRules: $this->vars->validationRules,
                request: $request,
                replacers: $this->vars->replacers,
            );
            // Check if module relation exists
            $model = $this->checkModuleRelation($model);
            // Model update
            if (!$model->update($requests)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error updating {$this->vars->level->name}");
            }

            PbCache::clearAll();

            // Update roles
            if (in_array($model->name, ['crud super-admin'])) {
                $superAdminRoles = PbRole::whereIn('name', ['super-admin'])->get()->modelKeys();
                $model->syncRoles($superAdminRoles);
            } elseif (in_array($model->name, ['developer options', 'read loggers', 'delete loggers', 'config loggers'])) {
                $developerRoles = PbRole::whereIn('name', ['super-admin', 'developer'])->get()->modelKeys();
                $model->syncRoles($developerRoles);
            } elseif (in_array($model->name, ['api access'])) {
                $apiRoles = PbRole::whereIn('name', ['super-admin', 'api-user'])->get()->modelKeys();
                $model->syncRoles($apiRoles);
            } elseif (in_array($model->name, ['manage app', 'admin roles permissions', 'config builder'])) {
                $adminRoles = PbRole::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                $model->syncRoles($adminRoles);
            } elseif (in_array($model->name, ['login'])) {
                $adminRoles = PbRole::all()->modelKeys();
                $model->syncRoles($adminRoles);
            } else {
                $adminRoles = PbRole::whereIn('name', ['super-admin', 'admin'])->get()->modelKeys();
                $model->syncRoles(
                    [
                        ...($roles && is_array($roles) ? $roles : ($roles ? [$roles] : [])),
                        ...($adminRoles && is_array($adminRoles) ? $adminRoles : ($adminRoles ? [$adminRoles] : []))
                    ]
                );
            }

            return $this->redirectResponseCRUDSuccess($request, 'update');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'update', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return Application|Redirector|RedirectResponse
     */
    public function destroy(Request $request, int $id): Redirector|RedirectResponse|Application
    {
        // Set cache/methods arguments
        $this->initArgs([
            'function' => 'destroy',
        ]);
        // Process
        try {
            if (!$model = $this->vars->level->modelPath::find($id)) {
                return $this->redirectResponseCRUDFail(request(), 'delete', "Error finding {$this->vars->level->name}");
            }
            // Push Unmodifiable Permissions
            $model = $this->pushUnmodifiablePermissions($model);
            // Check Permissions
            $this->checkPermissions($model);
            // Model delete
            if (!$model->delete()) {
                return $this->redirectResponseCRUDFail($request, 'delete', "Error deleting {$this->vars->level->name}");
            }

            PbCache::clearAll();

            return $this->redirectResponseCRUDSuccess($request, 'delete');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'delete', $e->getMessage());
        }
    }

    protected function pushUnmodifiablePermissions($model)
    {
        if (!app(PbCurrentUser::class)->hasRole('super-admin')) {
            array_push(
                $model->unmodifiableModels['name'],
                ...[
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
            );
            if (!app(PbCurrentUser::class)->hasRole('admin')) {
                array_push(
                    $model->unmodifiableModels['name'],
                    ...[
                        'read roles',
                        'read configs',
                        'read permissions',
                        'read navigations',
                    ]
                );
            }
        }
        return $model;
    }
}
