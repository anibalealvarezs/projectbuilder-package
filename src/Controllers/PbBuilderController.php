<?php

namespace Anibalealvarezs\Projectbuilder\Controllers;

use Anibalealvarezs\Projectbuilder\Facades\PbDebugbarFacade as Debug;
use Anibalealvarezs\Projectbuilder\Utilities\PbCache;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerListingTrait;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

use Auth;
use DB;
use ReflectionException;
use Session;

use Inertia\Response as InertiaResponse;

class PbBuilderController extends Controller
{
    use PbControllerTrait;
    use PbControllerListingTrait;

    /**
     * @var object
     */
    protected object $vars;

    function __construct(Request $request, $crud_perms = false)
    {
        $this->initVars();
        $this->vars->helper->prefix = ($this->vars->helper->prefix ?? resolve($this->vars->helper->class)->prefix);
        $this->vars->helper->vendor = ($this->vars->helper->vendor ?? resolve($this->vars->helper->class)->vendor);
        $this->vars->helper->package = ($this->vars->helper->package ?? resolve($this->vars->helper->class)->package);
        if (!isset($this->vars->helper->keys['level'])) {
            $this->vars->helper->keys['level'] = $this->vars->keys['level'] ?? 'Builder';
        }
        foreach (modelsLevels() as $value) {
            if (isset($this->vars->helper->keys[$value])) {
                $this->vars->{$value} = $this->buildControllerVars($this->vars->helper->keys[$value]);
            }
        }
        $this->vars->inertiaRoot = ($this->vars->inertiaRoot ?? $this->vars->helper->package . '::app');
        $this->vars->viewModelName = ($this->vars->viewModelName ?? $this->vars->level->names);
        if ($crud_perms) {
            // Middlewares
            foreach (methodsByPermission() as $key => $value) {
                $this->middleware('role_or_permission:' . $key . ' ' . $this->vars->level->names, $value);
            }
        }
        $this->vars->required = $this->getRequired();
        $this->vars->request = $request;
        $this->vars->sortable = isset($this->vars->level->modelPath::$sortable) && $this->vars->level->modelPath::$sortable;
        $this->vars->cacheObjects = [];
        $this->vars->config = [];
        $this->vars->runArgs = [
            'package' => $this->vars->helper->package,
            'model' => $this->vars->level->names,
        ];

        self::$item = $this->vars->level->name;
        self::$route = $this->vars->level->names;
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
        string $route = 'level'
    ): InertiaResponse|JsonResponse|RedirectResponse {

        $this->startController();

        // Set cache/methods arguments
        $this->initArgs([
            'class' => 'builder_controller',
            'pagination' => isset($this->vars->runArgs['pagination']) && $this->vars->runArgs['pagination'] ?: ['page' => $page, 'perpage' => $perpage, 'orderby' => $orderby, 'field' => $field, 'order' => $order]
        ]);

        // Load model config
        if (!$this->vars->config) {
            $this->measuredRun(return: $this->vars->config, name: getClassName(__CLASS__) . ' - model config load', args: [
                'closure' => fn() => $this->vars->level->modelPath::getCrudConfig(true),
                'modelFunction' => 'getCrudConfig',
            ]);
        }

        // Set additional config attributes
        $this->setAdditionalVars();

        // Sort and Paginate
        $this->measuredRun(return: $this->vars->listing, name: getClassName(__CLASS__) . ' - sorting and pagination', args: [
                'closure' => fn() => self::buildListingRow($this->vars->config),
                'modelFunction' => 'sortAndPaginate',
            ],
        );

        // Build models list
        $this->measuredRun(return: $arrayElements, name: getClassName(__CLASS__) . ' - models list build', args: [
            'closure' => function() use ($element, $multiple) {
                return $this->buildModelsArray($element, $multiple, null, true);
            },
            'modelFunction' => 'buildModelsArray',
        ]);

        Debug::add($arrayElements, 'data');

        $this->stopController();

        return $this->renderResponse($this->buildRouteString($route, 'index'), (array) $arrayElements);
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
        $this->startController();

        // Set cache/methods arguments
        $this->initArgs([
            'class' => 'builder_controller',
            'function' => 'create',
            'byRoles' => true,
        ]);

        if (!$this->vars->config) {
            // Load model config
            $this->measuredRun(return: $this->vars->config, name: getClassName(__CLASS__) . ' - model config load', args: [
                'closure' => fn() => $this->vars->level->modelPath::getCrudConfig(true),
                'modelFunction' => 'getCrudConfig',
            ]);
        }

        Debug::add($this->vars->config['form'], 'formconfig');

        $this->stopController();

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
        // Validation
        if ($failed = $this->validateRequest($this->vars->validationRules, $request)) {
            return $failed;
        }

        // Process
        try {
            // Add requests
            $model = $this->processModelRequests(
                validationRules: $this->vars->validationRules,
                request: $request,
                replacers: $this->vars->replacers,
                model: resolve($this->vars->level->modelPath)->setLocale(app()->getLocale())
            );
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

        $this->startController();

        // Set cache/methods arguments
        $this->initArgs([
            'class' => 'builder_controller',
            'function' => 'show',
            'byRoles' => true,
            'model' => $this->vars->level->name,
        ]);

        if (!$element) {
            $this->measuredRun(return: $element, name: getClassName(__CLASS__) . ' - model find', args: [
                'closure' => fn() => $this->vars->level->modelPath::find($id),
                'modelFunction' => 'find',
                'modelId' => $id,
            ]);
        }

        if (!$element) {
            return $this->redirectResponseCRUDFail(request(), 'show', "Error finding {$this->vars->level->name}");
        }

        $this->checkPermissions($element);

        $this->measuredRun(return: $model, name: getClassName(__CLASS__) . ' - model data build', args: [
            'closure' => fn() => $this->buildModelsArray($element, $multiple, $id),
            'modelFunction' => 'buildModelsArray',
            'modelId' => $id,
        ]);

        if (!$model) {
            return $this->redirectResponseCRUDFail(request(), 'show', "Error finding {$this->vars->level->name}");
        }

        Debug::add($model, 'data');

        $this->stopController();

        return $this->renderResponse($this->buildRouteString($route, 'show'), $model);
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

        $this->startController();

        // Set cache/methods arguments
        $this->initArgs([
            'class' => 'builder_controller',
            'function' => 'edit',
            'byRoles' => true,
            'model' => $this->vars->level->name,
        ]);

        // Load model config
        if (!$this->vars->config) {
            $this->measuredRun(return: $this->vars->config, name: getClassName(__CLASS__) . ' - model config load', args: [
                'closure' => fn() => $this->vars->level->modelPath::getCrudConfig(true),
                'modelFunction' => 'getCrudConfig',
                'modelId' => $id,
            ]);
        }

        Debug::add($this->vars->config['form'], 'formconfig');

        if (!$element) {
            $this->measuredRun(return: $element, name: getClassName(__CLASS__) . ' - model find', args: [
                'closure' => fn() => $this->vars->level->modelPath::find($id),
                'modelFunction' => 'find',
                'modelId' => $id,
            ]);
        }

        if (!$element) {
            return $this->redirectResponseCRUDFail(request(), 'edit', "Error finding {$this->vars->level->name}");
        }

        $this->checkPermissions($element);

        $this->measuredRun(return: $model, name: getClassName(__CLASS__) . ' - model data build', args: [
            'closure' => fn() => $this->buildModelsArray($element, $multiple, $id),
            'modelFunction' => 'buildModelsArray',
            'modelId' => $id,
        ]);

        Debug::add($model, 'data');

        if (!$model) {
            return $this->redirectResponseCRUDFail(request(), 'edit', "Error finding {$this->vars->level->name}");
        }

        $this->stopController();

        return $this->renderResponse($this->buildRouteString($route, 'edit'), $model);
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
            // Check if module relation exists
            $model = $this->checkModuleRelation($model);
            // Model update
            if (!$model->update($requests)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error updating {$this->vars->level->name}");
            }
            // Clear cache
            PbCache::clearModel(
                package: $this->vars->helper->package,
                models: $this->vars->level->names,
                modelId: $id,
            );
            // Redirect
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
                return $this->redirectResponseCRUDFail($request, 'delete', "Error finding {$this->vars->level->name}");
            }
            // Check Permissions
            $this->checkPermissions($model);
            // Model delete
            if (!$model->delete()) {
                return $this->redirectResponseCRUDFail($request, 'delete', "Error deleting {$this->vars->level->name}");
            }
            // Clear cache
            PbCache::clearModel(
                package: $this->vars->helper->package,
                models: $this->vars->level->names,
                modelId: $id,
            );
            // Redirect
            return $this->redirectResponseCRUDSuccess($request, 'delete');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'delete', $e->getMessage());
        }
    }

    /**
     * Sort model elements
     *
     * @param Request $request
     * @param int $id
     * @return Application|Redirector|RedirectResponse|null
     */
    public function sort(Request $request, int $id): Redirector|RedirectResponse|Application|null
    {
        // Validation
        if ($failed = $this->validateRequest(['sortlist' => ['required']], $request)) {
            return $failed;
        }

        $sortList = $request['sortlist'];

        try {
            $n = 0;
            foreach ($sortList as $sortEl) {
                if ($id > 0) {
                    $query = $this->vars->level->modelPath::where($this->vars->sortingRef, $id)
                        ->where('id', $sortEl)->first();
                    if ($query) {
                        // Build model
                        $el = $this->vars->level->modelPath::find($query->id);
                    }
                } else {
                    // Build model
                    $el = $this->vars->level->modelPath::find($sortEl);
                }
                if (isset($el) && $el) {
                    $el->position = $n;
                    $el->save();
                }
                unset($el);
                $n++;
            }

            return $this->redirectResponseCRUDSuccess($request, 'sort');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request,
                $this->vars->level->key . ' could not be sorted! ' . $e->getMessage());
        }
    }

    /**
     * Sort model elements
     *
     * @param Request $request
     * @param int $id
     * @return Application|Redirector|RedirectResponse|null
     */
    public function enable(Request $request, int $id): Redirector|RedirectResponse|Application|null
    {
        // Set cache/methods arguments
        $this->initArgs([
            'function' => 'enable',
        ]);
        // Is enableable?
        if (!isset($this->vars->level->modelPath::$enableable) || !resolve($this->vars->level->modelPath)->isEditableBy(Auth::user()->id)) {
            return $this->redirectResponseCRUDFail($request, 'enable',
                $this->vars->level->key . ' is not enableable! ');
        }
        // Find model
        if (!$model = $this->vars->level->modelPath::find($id)) {
            return $this->redirectResponseCRUDFail($request, 'enable', "Error finding {$this->vars->level->name}");
        }
        // Check Permissions
        $this->checkPermissions($model);
        // Enable
        try {
            if ($model->enable()) {
                return $this->redirectResponseCRUDSuccess($request, 'enable');
            }
            return $this->redirectResponseCRUDFail($request, 'enable',
                $this->vars->level->key . ' could not be enabled due to an unknown error');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'enable',
                $this->vars->level->key . ' could not be enabled! ' . $e->getMessage());
        }
    }

    /**
     * Sort model elements
     *
     * @param Request $request
     * @param int $id
     * @return Application|Redirector|RedirectResponse|null
     */
    public function disable(Request $request, int $id): Redirector|RedirectResponse|Application|null
    {
        // Set cache/methods arguments
        $this->initArgs([
            'function' => 'disable',
        ]);
        // Is disableable?
        if (!isset($this->vars->level->modelPath::$enableable) || !resolve($this->vars->level->modelPath)->isEditableBy(Auth::user()->id)) {
            return $this->redirectResponseCRUDFail($request, 'disable',
                $this->vars->level->key . ' is not disableable! ');
        }
        // Find model
        if (!$model = $this->vars->level->modelPath::find($id)) {
            return $this->redirectResponseCRUDFail($request, 'disable', "Error finding {$this->vars->level->name}");
        }
        // Check Permissions
        $this->checkPermissions($model);
        // Disable
        try {
            if ($model->disable()) {
                return $this->redirectResponseCRUDSuccess($request, 'disable');
            }
            return $this->redirectResponseCRUDFail($request, 'disable',
                $this->vars->level->key . ' could not be disabled due to an unknown error');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'disable',
                $this->vars->level->key . ' could not be disabled! ' . $e->getMessage());
        }
    }
}
