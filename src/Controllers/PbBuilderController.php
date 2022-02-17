<?php

namespace Anibalealvarezs\Projectbuilder\Controllers;

use Anibalealvarezs\Projectbuilder\Facades\PbDebugbarFacade as Debug;
use Anibalealvarezs\Projectbuilder\Utilities\PbCache;
use Anibalealvarezs\Projectbuilder\Utilities\PbUtilities;
use Anibalealvarezs\Projectbuilder\Utilities\PbShares;
use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Models\PbModule;
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
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;
use Session;

use Inertia\Inertia;
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
     * @throws ReflectionException|InvalidArgumentException
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

        Debug::start('builder_controller', 'builder crud controller');

        $this->vars->allowed = [
            'create ' . $this->vars->level->names => 'create',
            'update ' . $this->vars->level->names => 'update',
            'delete ' . $this->vars->level->names => 'delete',
        ];

        Debug::measure(
            'builder crud controller - model config load',
            function() use (&$config, $page, $perpage, $orderby, $field, $order) {
                $cached = PbCache::run(
                    closure: fn() => (isset($this->vars->config) && $this->vars->config) ? $this->vars->config : $this->vars->level->modelPath::getCrudConfig(true),
                    package: $this->vars->helper->package,
                    class: __CLASS__,
                    model: $this->vars->level->names,
                    modelFunction: 'getCrudConfig',
                    pagination: ['page' => $page, 'perpage' => $perpage, 'orderby' => $orderby, 'field' => $field, 'order' => $order],
                    byRoles: !($this->vars->level->names == 'users'),
                    byUser: true,
                );
                $config = $cached['data'];
                $this->vars->cacheObjects[] = $cached['index'];
            }
        );

        if (!isset($config['fields']['actions'])) {
            $config['fields']['actions'] = [
                'update' => [],
                'delete' => []
            ];
        }

        Debug::measure(
            'builder crud controller - model config additional',
            function() use (&$config, $page, $perpage, $orderby, $field, $order) {
                $cached = PbCache::run(
                    closure: fn() => PbShares::allowed($this->vars->allowed)['allowed'],
                    package: $this->vars->helper->package,
                    class: __CLASS__,
                    model: $this->vars->level->names,
                    modelFunction: 'sharesAllowed',
                    pagination: ['page' => $page, 'perpage' => $perpage, 'orderby' => $orderby, 'field' => $field, 'order' => $order],
                    byRoles: !($this->vars->level->names == 'users'),
                    byUser: true,
                );
                $config['enabled_actions'] = $cached['data'];
                $this->vars->cacheObjects[] = $cached['index'];
            }
        );

        if (!isset($config['model'])) {
            $config['model'] = $this->vars->level->modelPath;
        }
        if (!isset($config['pagination']['page']) || !$config['pagination']['page']) {
            $config['pagination']['page'] = $page;
        }

        Debug::measure(
            'builder crud controller - sorting and pagination',
            function() use ($config, $page, $perpage, $orderby, $field, $order) {
                $this->vars->sortable = $this->vars->sortable && app(PbCurrentUser::class)->hasPermissionTo('update '.resolve($this->vars->level->modelPath)->getTable());
                $this->vars->formconfig = ($config['formconfig'] ?? []);
                $this->vars->pagination = !$this->vars->sortable ? $config['pagination'] : [];
                $this->vars->heading = $config['heading'];
                $this->vars->orderby = !$this->vars->sortable && $orderby ? ['field' => $field, 'order' => $order] : [];
                $cached = PbCache::run(
                    closure: fn() => self::buildListingRow($config),
                    package: $this->vars->helper->package,
                    class: __CLASS__,
                    model: $this->vars->level->names,
                    modelFunction: 'sortAndPaginate',
                    pagination: ['page' => $page, 'perpage' => $perpage, 'orderby' => $orderby, 'field' => $field, 'order' => $order],
                    byRoles: !($this->vars->level->names == 'users'),
                    byUser: true,
                );
                $this->vars->listing = $cached['data'];
                $this->vars->cacheObjects[] = $cached['index'];
            }
        );

        Debug::measure(
            'builder crud controller - model list build',
            function() use (&$arrayElements, $order, $field, $orderby, $perpage, $page, $multiple, $element) {
                $cached = PbCache::run(
                    closure: function() use (&$arrayElements, $element, $multiple, $page, $perpage, $orderby, $field, $order) {
                        return $this->buildModelsArray($element, $multiple, null, true, $page, $perpage, $orderby, $field, $order);
                    },
                    package: $this->vars->helper->package,
                    class: __CLASS__,
                    model: $this->vars->level->names,
                    modelFunction: 'buildModelsArray',
                    pagination: ['page' => $page, 'perpage' => $perpage, 'orderby' => $orderby, 'field' => $field, 'order' => $order],
                    byRoles: !($this->vars->level->names == 'users'),
                    byUser: true,
                );
                $arrayElements = $cached['data'];
                $this->vars->cacheObjects[] = $cached['index'];
            }
        );

        Debug::add($arrayElements, 'data');
        Debug::add($this->vars->cacheObjects, 'caches', true);

        Debug::stop('builder_controller');

        return $this->renderResponse($this->buildRouteString($route, 'index'), (array) $arrayElements);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $route
     * @return InertiaResponse|JsonResponse
     * @throws ReflectionException|InvalidArgumentException
     */
    public function create(string $route = 'level'): InertiaResponse|JsonResponse
    {
        Debug::start('builder_controller', 'builder crud controller');

        Debug::measure(
            'builder crud controller - model config load',
            function() use (&$config) {
                $cached = PbCache::run(
                    closure: fn() => (isset($this->vars->config) && $this->vars->config) ? $this->vars->config : $this->vars->level->modelPath::getCrudConfig(true),
                    package: $this->vars->helper->package,
                    class: __CLASS__,
                    function: 'create',
                    model: $this->vars->level->names,
                    modelFunction: 'getCrudConfig',
                    byRoles: true,
                );
                $this->vars->formconfig = $cached['data']['formconfig'];
                $this->vars->cacheObjects[] = $cached['index'];
            }
        );

        Debug::add($this->vars->formconfig, 'formconfig');
        Debug::add($this->vars->cacheObjects, 'caches', true);

        Debug::stop('builder_controller');

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
            if ($model->hasRelation('module') && ($request->input('module') > 0) &&  $module = PbModule::find($request->input('module'))) {
                $model->module()->associate($module);
            }
            // Model save
            if (!$model->save()) {
                return $this->redirectResponseCRUDFail($request, 'create', "Error saving {$this->vars->level->name}");
            }

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
     * @throws ReflectionException|InvalidArgumentException
     */
    public function show(
        int $id,
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): Application|RedirectResponse|Redirector|InertiaResponse|JsonResponse {

        Debug::start('builder_controller', 'builder crud controller');

        if (!$element) {
            Debug::measure(
                'builder crud controller - model find',
                function() use (&$element, $id) {
                    $cached = PbCache::run(
                        closure: fn() => $this->vars->level->modelPath::find($id),
                        package: $this->vars->helper->package,
                        class: __CLASS__,
                        function: 'show',
                        model: $this->vars->level->name,
                        modelFunction: 'find',
                        modelId: $id,
                        byRoles: true,
                    );
                    $element = $cached['data'];
                    $this->vars->cacheObjects[] = $cached['index'];
                }
            );

            if (!$element) {
                return $this->redirectResponseCRUDFail(request(), 'show', "Error finding {$this->vars->level->name}");
            }
        }

        Debug::start('permissions_check', 'permissions check');
        if ($this->isUnreadableModel($element)) {
            return $this->redirectResponseCRUDFail(request(), 'show', "This {$this->vars->level->name} cannot be shown");
        }
        if (!$element->isViewableBy(Auth::user()->id)) {
            return $this->redirectResponseCRUDFail(request(), 'show', "You don't have permission to view this {$this->vars->level->name}");
        }
        Debug::stop('permissions_check');

        Debug::measure(
            'builder crud controller - model data build',
            function() use (&$model, $element, $multiple, $id) {
                $cached = PbCache::run(
                    closure: fn() => $this->buildModelsArray($element, $multiple, $id),
                    package: $this->vars->helper->package,
                    class: __CLASS__,
                    function: 'show',
                    model: $this->vars->level->name,
                    modelFunction: 'buildModelsArray',
                    modelId: $id,
                    byRoles: true,
                );
                $model = $cached['data'];
                $this->vars->cacheObjects[] = $cached['index'];
            }
        );

        Debug::add($model, 'data');
        Debug::add($this->vars->cacheObjects, 'caches', true);

        if (!$model) {
            return $this->redirectResponseCRUDFail(request(), 'show', "Error finding {$this->vars->level->name}");
        }

        Debug::stop('builder_controller');

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
     * @throws ReflectionException|InvalidArgumentException
     */
    public function edit(
        int $id,
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): RedirectResponse|InertiaResponse|JsonResponse {

        Debug::start('builder_controller', 'builder crud controller');

        Debug::measure('builder crud controller - model config load', function() {
            $this->vars->formconfig = $this->vars->level->modelPath::getCrudConfig(true)['formconfig'];
        });

        Debug::add($this->vars->formconfig, 'formconfig');

        if (!$element) {
            Debug::measure(
                'builder crud controller - model find',
                function() use (&$element, $id) {
                    $cached = PbCache::run(
                        closure: fn() => $this->vars->level->modelPath::find($id),
                        package: $this->vars->helper->package,
                        class: __CLASS__,
                        function: 'edit',
                        model: $this->vars->level->name,
                        modelFunction: 'find',
                        modelId: $id,
                        byRoles: true,
                    );
                    $element = $cached['data'];
                    $this->vars->cacheObjects[] = $cached['index'];
                }
            );

            if (!$element) {
                return $this->redirectResponseCRUDFail(request(), 'edit', "Error finding {$this->vars->level->name}");
            }
        }

        Debug::start('permissions_check', 'permissions check');
        if ($this->isUnreadableModel($element)) {
            return $this->redirectResponseCRUDFail(request(), 'edit', "This {$this->vars->level->name} cannot be modified");
        }
        if (!$element->isEditableBy(Auth::user()->id)) {
            return $this->redirectResponseCRUDFail(request(), 'edit', "You don't have permission to edit this {$this->vars->level->name}");
        }
        Debug::stop('permissions_check');

        Debug::measure(
            'builder crud controller - model data build',
            function() use (&$model, $element, $multiple, $id) {
                $cached = PbCache::run(
                    closure: fn() => $this->buildModelsArray($element, $multiple, $id),
                    package: $this->vars->helper->package,
                    class: __CLASS__,
                    function: 'edit',
                    model: $this->vars->level->name,
                    modelFunction: 'buildModelsArray',
                    modelId: $id,
                    byRoles: true,
                );
                $model = $cached['data'];
                $this->vars->cacheObjects[] = $cached['index'];
            }
        );

        Debug::add($model, 'data');
        Debug::add($this->vars->cacheObjects, 'caches', true);

        if (!$model) {
            return $this->redirectResponseCRUDFail(request(), 'edit', "Error finding {$this->vars->level->name}");
        }

        Debug::stop('builder_controller');

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
        Debug::stop('model_controller');
        Debug::start('builder_controller', 'builder crud controller');

        // Validation
        if ($failed = $this->validateRequest($this->vars->validationRules, $request)) {
            return $failed;
        }

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
            $requests = $this->processModelRequests(
                validationRules: $this->vars->validationRules,
                request: $request,
                replacers: $this->vars->replacers,
            );
            // Check if module relation exists
            if ($model->hasRelation('module') && ($request->input('module') > 0) &&  $module = PbModule::find($request->input('module'))) {
                $model->module()->associate($module);
            }
            // Model update
            if (!$model->update($requests)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error updating {$this->vars->level->name}");
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
        Debug::stop('model_controller');
        Debug::start('builder_controller', 'builder crud controller');

        if (!$model = $this->vars->level->modelPath::find($id)) {
            return $this->redirectResponseCRUDFail($request, 'delete', "Error finding {$this->vars->level->name}");
        }
        if (!$model->isDeletableBy(Auth::user()->id)) {
            return $this->redirectResponseCRUDFail($request, 'delete', "You don't have permission to edit this {$this->vars->level->name}");
        }
        if ($this->isUndeletableModel($model)) {
            return $this->redirectResponseCRUDFail($request, 'delete', "This {$this->vars->level->name} cannot be deleted");
        }
        // Process
        try {
            // Model delete
            if (!$model->delete()) {
                return $this->redirectResponseCRUDFail($request, 'delete', "Error deleting {$this->vars->level->name}");
            }

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
        Debug::stop('model_controller');
        Debug::start('builder_controller', 'builder crud controller');

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
        Debug::stop('model_controller');
        Debug::start('builder_controller', 'builder crud controller');

        if (isset($this->vars->level->modelPath::$enableable) && resolve($this->vars->level->modelPath)->isEditableBy(Auth::user()->id)) {
            $model = $this->vars->level->modelPath::find($id);
            if ($this->isUnmodifiableModel($model)) {
                return $this->redirectResponseCRUDFail($request, 'enable', "This {$this->vars->level->name} cannot be modified");
            }
            if (!$model->isEditableBy(Auth::user()->id)) {
                return $this->redirectResponseCRUDFail($request, 'enable', "You don't have permission to edit this {$this->vars->level->name}");
            }
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
        } else {
            return $this->redirectResponseCRUDFail($request, 'enable',
                $this->vars->level->key . ' is not enableable! ');
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
        Debug::stop('model_controller');
        Debug::start('builder_controller', 'builder crud controller');

        if (isset($this->vars->level->modelPath::$enableable) && resolve($this->vars->level->modelPath)->isEditableBy(Auth::user()->id)) {
            $model = $this->vars->level->modelPath::find($id);
            if ($this->isUnmodifiableModel($model)) {
                return $this->redirectResponseCRUDFail($request, 'disable', "This {$this->vars->level->name} cannot be modified");
            }
            if (!$model->isEditableBy(Auth::user()->id)) {
                return $this->redirectResponseCRUDFail($request, 'disable', "You don't have permission to edit this {$this->vars->level->name}");
            }
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
        } else {
            return $this->redirectResponseCRUDFail($request, 'disable',
                $this->vars->level->key . ' is not disableable! ');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param null $element
     * @param bool $multiple
     * @param null $id
     * @param bool $plural
     * @param int $page
     * @param int $perpage
     * @param string|null $orderby
     * @param string $field
     * @param string $order
     * @return array
     */
    protected function buildModelsArray(
        $element = null,
        bool $multiple = false,
        $id = null,
        bool $plural = false,
        int $page = 1,
        int $perpage = 0,
        string $orderby = null,
        string $field = 'id',
        string $order = 'asc'
    ): array {
        $arrayElements = [];
        if ($element) {
            if ($multiple) {
                foreach ($element as $key => $value) {
                    $arrayElements[($value['size'] == 'multiple' ? $this->vars->{$key}->prefixNames : $this->vars->{$key}->prefixName)] = $value['object'];
                }
            } else {
                $arrayElements[($plural ? $this->vars->level->prefixNames : $this->vars->level->prefixName)] = $element;
            }
        } else {
            $arrayElements[($plural ?
                $this->vars->level->prefixNames :
                $this->vars->level->prefixName
            )] = ($id ?
                $this->vars->level->modelPath::find($id) :
                $this->buildPaginatedAndOrderedModel(
                    page: $page,
                    perpage: $perpage,
                    orderby: $orderby,
                    field: $field,
                    order: $order,
                )
            );
        }

        return $arrayElements;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    protected function shareVars()
    {
        $shared = [
            ...$this->globalInertiaShare(),
            ...PbShares::allowed($this->vars->allowed),
            ...PbShares::list($this->vars->shares),
            ...['sort' => $this->vars->sortable],
            ...['showpos' => $this->vars->showPosition],
            ...['showid' => $this->vars->showId],
            ...['model' => $this->vars->viewModelName],
            ...['required' => $this->vars->required],
            ...['defaults' => $this->getDefaults()],
            ...['listing' => $this->vars->listing],
            ...['formconfig' => $this->vars->formconfig],
            ...['pagination' => (isset($this->vars->pagination) && $this->vars->pagination) ? $this->vars->pagination : ['location' => 'none']],
            ...['heading' => (isset($this->vars->heading) && $this->vars->heading) ? $this->vars->heading : ['location' => 'none']],
            ...['orderby' => $this->vars->orderby ?? []],
        ];
        Inertia::share('shared', $shared);
        Debug::add($shared, 'shared');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    protected function getDefaults()
    {
        $defaults = (object)[];
        foreach ($this->vars->defaults as $key => $value) {
            $defaults->$key = match ($key) {
                'lang' => PbLanguage::findByCode($value),
                'country' => PbCountry::findByCode($value),
                default => $value,
            };
        }
        return $defaults;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    protected function getRequired()
    {
        $required = [];
        foreach ($this->vars->validationRules as $key => $value) {
            if (in_array('required', $value)) {
                array_push($required, $key);
            }
        }
        return $required;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param array $validationRules
     * @param Request $request
     * @param array $replacers
     * @param null $model
     * @return array|object
     */
    protected function processModelRequests(
        array $validationRules,
        Request $request,
        array $replacers,
        $model = null
    ): array|object
    {
        $keys = [];
        foreach ($validationRules as $vrKey => $vr) {
            if (isset($replacers[$vrKey])) {
                ${$replacers[$vrKey]} = $request[$vrKey];
                array_push($keys, $replacers[$vrKey]);
            } else {
                ${$vrKey} = $request[$vrKey];
                array_push($keys, $vrKey);
            }
        }

        if (!$model) {
            $requests = [];
            foreach ($keys as $key) {
                if (!in_array($key, $this->vars->modelExclude)) {
                    $requests[$key] = ${$key};
                }
            }
            return $requests;
        }

        foreach ($keys as $key) {
            if (!in_array($key, $this->vars->modelExclude)) {
                $model->$key = ${$key};
            }
        }

        return $model;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $view
     * @param array $elements
     * @param bool $nullable
     * @return JsonResponse|InertiaResponse
     */
    protected function renderResponse($view, array $elements = [], bool $nullable = false): JsonResponse|InertiaResponse
    {
        // If not API
        if (!isApi($this->vars->request)) {
            Debug::start('builder_controller_response_building', 'builder crud controller - response building');

            Debug::measure('builder crud controller - share building', function() {
                $this->shareVars();
            });

            Inertia::setRootView($this->vars->inertiaRoot);

            Debug::stop('builder_controller');
            Debug::stop('builder_controller_response_building');

            return Inertia::render($view, $elements);
        }

        // If API
        if ($elements || $nullable) {
            return $this->handleJSONResponse($elements, 'Operation Successful');
        } else {
            return $this->handleJSONError('Operation Failed');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $route
     * @param $type
     * @return string
     */
    protected function buildRouteString($route, $type): string
    {
        return $this->vars->{$route}->viewsPath .
            $this->buildFile(
                $type,
                ['singular' => $this->vars->{$route}->key, 'plural' => $this->vars->{$route}->keys]
            );
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $key
     * @return object
     */
    public function buildControllerVars($key): object
    {
        $object = (object)[];
        $object->key = $key;
        $object->keys = Str::plural($key);
        $object->name = strtolower($key);
        $object->names = Str::plural($object->name);
        $object->prefixName = strtolower($this->vars->helper->prefix . $key);
        $object->prefixNames = Str::plural($object->prefixName);
        $object->modelPath = $this->vars->helper->vendor . "\\" . $this->vars->helper->package . "\\Models\\" . $this->vars->helper->prefix . $key;
        $object->viewsPath = $this->vars->helper->package . "/" . $object->keys . "/";
        $object->table = resolve($object->modelPath)->getTable();
        return $object;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $vars
     * @return void
     */
    public function varsObject($vars)
    {
        if (!isset($this->vars)) {
            $this->vars = (object)[];
        }
        foreach ($vars as $key => $value) {
            $this->vars->{$key} = $value;
        }
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return void
     */
    public function initVars()
    {
        $this->vars = ($this->vars ?? (object)[]);
        $this->vars->helper = ($this->vars->helper ?? (object)[]);
        $this->vars->helper->class =
            ($this->vars->helper->class ??
                app(PbUtilities::class)->vendor . '\\' . app(PbUtilities::class)->package . '\\Utilities\\' . app(PbUtilities::class)->prefix . 'Utilities'
            );
        $this->vars->validationRules = ($this->vars->validationRules ?? []);
        $this->vars->allowed = ($this->vars->allowed ?? []);
        $this->vars->shares = ($this->vars->shares ?? []);
        $this->vars->modelExclude = ($this->vars->modelExclude ?? []);
        $this->vars->listing = ($this->vars->listing ?? []);
        $this->vars->formconfig = ($this->vars->formconfig ?? []);
        $this->vars->replacers = ($this->vars->replacers ?? []);
        $this->vars->defaults = ($this->vars->defaults ?? (object)[]);
        $this->vars->showPosition = ($this->vars->showPosition ?? false);
        $this->vars->showId = ($this->vars->showId ?? true);
        $this->vars->sortingRef = ($this->vars->sortingRef ?? "");
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return void
     */
    public function pushRequired($array)
    {
        $this->vars->required = [
            ...$this->vars->required,
            ...$array
        ];
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return void
     */
    public function pushValidationRules($array)
    {
        $this->vars->validationRules = [
            ...$this->vars->validationRules,
            ...$array
        ];
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return void
     */
    public function arraytify($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = $value->toArray();
        }
        return $result;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $model
     * @return bool
     */
    public function isUndeletableModel($model): bool
    {
        if (isset($model->undeletableModels)) {
            foreach($model->undeletableModels as $key => $value) {
                if (in_array($model->{$key}, $value)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $model
     * @return bool
     */
    public function isUnmodifiableModel($model): bool
    {
        if (isset($model->unmodifiableModels)) {
            foreach($model->unmodifiableModels as $key => $value) {
                if (in_array($model->{$key}, $value)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $model
     * @return bool
     */
    public function isUnreadableModel($model): bool
    {
        if (isset($model->unreadableModels)) {
            foreach($model->unreadableModels as $key => $value) {
                if (in_array($model->{$key}, $value)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $model
     * @return bool
     */
    public function isUnconfigurableModel($model): bool
    {
        if (isset($model->unconfigurableModels)) {
            foreach($model->unconfigurableModels as $key => $value) {
                if (in_array($model->{$key}, $value)) {
                    return true;
                }
            }
        }
        return false;
    }
}
