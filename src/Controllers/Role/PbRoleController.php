<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Role;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;
use Anibalealvarezs\Projectbuilder\Utilities\PbCache;

use App\Http\Requests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use ReflectionException;
use Session;

use Inertia\Response as InertiaResponse;

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

        $this->pushRequired(['name']);

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
                $query = $this->vars->level->modelPath::withPublicRelations()->whereNotIn('name', ['super-admin', 'developer', 'api-user']);
                if (!app(PbCurrentUser::class)->hasRole('super-admin')) {
                    $query->whereNotIn('name', ['admin']);
                }
                return $this->buildPaginatedAndOrderedModel(
                    query: $query,
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
     * Show the form for creating a new resource.
     *
     * @param string $route
     * @return InertiaResponse|JsonResponse
     * @throws ReflectionException
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
     * @return Application|Redirector|RedirectResponse|JsonResponse|null
     */
    public function store(Request $request): Redirector|RedirectResponse|Application|JsonResponse|null
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
            // Model save
            if (!$model->save()) {
                return $this->redirectResponseCRUDFail($request, 'create', "Error saving {$this->vars->level->name}");
            }

            PbCache::clearIndex(
                package: $this->vars->helper->package,
                models: $this->vars->level->names,
            );

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
     * @throws ReflectionException
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
     * @return RedirectResponse|InertiaResponse|JsonResponse
     * @throws ReflectionException
     */
    public function edit(int $id, $element = null, bool $multiple = false, string $route = 'level'): RedirectResponse|InertiaResponse|JsonResponse
    {
        $this->startController(getClassName(__CLASS__));

        $this->pushRequired(['name']);

        // Set cache/methods arguments
        $this->initArgs([
            'class' => 'model_controller',
            'function' => 'edit',
            'model' => $this->vars->level->name,
            'byRoles' => true,
        ]);

        $this->measuredRun(return: $model, name: getClassName(__CLASS__) . ' - model find', args: [
            'closure' => fn() => $this->vars->level->modelPath::withPublicRelations()->whereNotIn('name', ['super-admin', 'developer', 'api-user'])->find($id),
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
     * @return Application|Redirector|RedirectResponse|JsonResponse|null
     */
    public function update(Request $request, int $id): Redirector|RedirectResponse|Application|JsonResponse|null
    {
        $this->pushValidationRules([
            'name' => ['required', 'max:20', Rule::unique($this->vars->level->table)->ignore($id)],
        ]);

        // Validation
        if ($failed = $this->validateRequest($this->vars->validationRules, $request)) {
            return $failed;
        }

        $permissions = $request->input('permissions');

        // Set cache/methods arguments
        $this->initArgs([
            'function' => 'update',
        ]);

        // Process
        try {
            // Build model
            if (!$model = $this->vars->level->modelPath::find($id)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error finding {$this->vars->level->name}");
            }
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
            // Model update
            if (!$model->update($requests)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error updating {$this->vars->level->name}");
            }

            PbCache::clearAll();

            // Update permissions
            $this->loadDefaultPermissions();
            if (
                (in_array($model->name, ['super-admin', 'admin']) && app(PbCurrentUser::class)->hasRole('super-admin')) ||
                (($model->name == 'admin') && app(PbCurrentUser::class)->hasRole('admin')) ||
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

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return Application|Redirector|RedirectResponse|JsonResponse
     */
    public function destroy(Request $request, int $id): Redirector|RedirectResponse|Application|JsonResponse
    {
        // Set cache/methods arguments
        $this->initArgs([
            'function' => 'destroy',
        ]);
        // Process
        try {
            if (!$model = $this->vars->level->modelPath::find($id)) {
                return $this->redirectResponseCRUDFail($request, 'delete', "Error finding {$this->vars->level->name}");
            }
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
