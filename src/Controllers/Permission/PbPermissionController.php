<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Permission;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use Anibalealvarezs\Projectbuilder\Models\PbRole;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use App\Http\Requests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

use Auth;
use DB;

use Inertia\Response as InertiaResponse;

use Session;

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
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse|RedirectResponse
     */
    public function index(
        int $page = 1,
        int $perpage = 0,
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): InertiaResponse|JsonResponse|RedirectResponse {
        $me = PbUser::current();
        $toExclude = ['crud super-admin'];
        if (!$me->hasRole('super-admin')) {
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
            if (!$me->hasRole('admin')) {
                $toExclude = [
                    ...$toExclude,
                    ...['login', 'create users', 'update users', 'delete users']
                ];
            }
        }

        $config = $this->vars->level->modelPath::getCrudConfig();
        if (!$perpage && isset($config['pagination']['per_page']) && $config['pagination']['per_page']) {
            $perpage = $config['pagination']['per_page'];
        }

        $model = $this->vars->level->modelPath::whereNotIn('name', $toExclude)->withPublicRelations()
            ->paginate($perpage ?? PbConfig::getValueByKey('_DEFAULT_TABLE_SIZE_') ?: 10, ['*'], 'page', $page ?? 1);

        return parent::index($page, $perpage, $model);
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
            $model = (new $this->vars->level->modelPath())->setLocale(app()->getLocale());
            // Add requests
            $model = $this->processModelRequests($this->vars->validationRules, $request, $this->vars->replacers, $model);
            // Add additional fields values
            $model->guard_name = 'admin';
            // Model save
            if (!$model->save()) {
                return $this->redirectResponseCRUDFail($request, 'create', "Error saving {$this->vars->level->name}");
            }
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
     * @return InertiaResponse|JsonResponse
     */
    public function edit(
        int $id,
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): InertiaResponse|JsonResponse {
        $model = $this->vars->level->modelPath::withPublicRelations()->findOrFail($id);

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

        // Process
        try {
            // Build model
            $model = $this->vars->level->modelPath::find($id)->setLocale(app()->getLocale());
            // Build requests
            $requests = $this->processModelRequests($this->vars->validationRules, $request, $this->vars->replacers);
            // Model update
            if (!$model->update($requests)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error updating {$this->vars->level->name}");
            }
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
        // Process
        try {
            $model = $this->vars->level->modelPath::findOrFail($id);
            //Make it impossible to delete these specific permissions
            if (in_array($model->name, [
                'admin roles permissions',
                'manage app',
                'crud super-admin',
                'config builder',
                'developer options',
                'read loggers',
                'delete loggers',
                'config loggers',
                'api access',
                'login'
            ])) {
                $request->session()->flash('flash.banner', 'This permission can not be deleted!');
                $request->session()->flash('flash.bannerStyle', 'danger');

                return redirect()->route($this->vars->level->names . '.index');
            }
            // Model delete
            if (!$model->delete()) {
                return $this->redirectResponseCRUDFail($request, 'delete', "Error deleting {$this->vars->level->name}");
            }

            return $this->redirectResponseCRUDSuccess($request, 'delete');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'delete', $e->getMessage());
        }
    }
}
