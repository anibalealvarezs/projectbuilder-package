<?php

namespace Anibalealvarezs\Projectbuilder\Controllers;

use Anibalealvarezs\Projectbuilder\Helpers\PbDebugbar;
use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
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
        $this->vars->helper->prefix = ($this->vars->helper->prefix ?? $this->vars->helper->class::getDefault('prefix'));
        $this->vars->helper->vendor = ($this->vars->helper->vendor ?? $this->vars->helper->class::getDefault('vendor'));
        $this->vars->helper->package = ($this->vars->helper->package ?? $this->vars->helper->class::getDefault('package'));
        if (!isset($this->vars->helper->keys['level'])) {
            $this->vars->helper->keys['level'] = ($this->vars->keys['level'] ?? 'Builder');
        }
        foreach ($this->vars->helper->class::getModelsLevels() as $value) {
            if (isset($this->vars->helper->keys[$value])) {
                $this->vars->{$value} = $this->buildControllerVars($this->vars->helper->keys[$value]);
            }
        }
        $this->vars->inertiaRoot = ($this->vars->inertiaRoot ?? $this->vars->helper->package . '::app');
        $this->vars->viewModelName = ($this->vars->viewModelName ?? $this->vars->level->names);
        if ($crud_perms) {
            // Middlewares
            foreach ($this->vars->helper->class::getMethodsByPermission() as $key => $value) {
                $this->middleware('role_or_permission:' . $key . ' ' . $this->vars->level->names, $value);
            }
        }
        $this->vars->required = $this->getRequired();
        $this->vars->request = $request;
        $this->vars->sortable = isset($this->vars->level->modelPath::$sortable) && $this->vars->level->modelPath::$sortable;

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

        $this->vars->allowed = [
            'create ' . $this->vars->level->names => 'create',
            'update ' . $this->vars->level->names => 'update',
            'delete ' . $this->vars->level->names => 'delete',
        ];

        $config = $this->vars->level->modelPath::getCrudConfig();
        if (!isset($config['fields']['actions'])) {
            $config['fields']['actions'] = [
                'update' => [],
                'delete' => []
            ];
        }
        $config['enabled_actions'] = Shares::allowed($this->vars->allowed)['allowed'];
        if (!isset($config['model'])) {
            $config['model'] = $this->vars->level->modelPath;
        }
        if (!isset($config['pagination']['page']) || !$config['pagination']['page']) {
            $config['pagination']['page'] = $page;
        }

        $this->vars->listing = self::buildListingRow($config);
        $this->vars->sortable = $this->vars->sortable && app(PbCurrentUser::class)->hasPermissionTo('update '.(new $this->vars->level->modelPath)->getTable());
        $this->vars->formconfig = $config['formconfig'];
        $this->vars->pagination = !$this->vars->sortable ? $config['pagination'] : [];
        $this->vars->heading = $config['heading'];
        $this->vars->orderby = !$this->vars->sortable && $orderby ? ['field' => $field, 'order' => $order] : [];

        $arrayElements = $this->buildModelsArray($element, $multiple, null, true, $page, $perpage, $orderby, $field, $order);
        PbDebugbar::addMessage($this->arraytify($arrayElements), 'data');

        return $this->renderResponse($this->buildRouteString($route, 'index'), $arrayElements);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $route
     * @return InertiaResponse|JsonResponse
     */
    public function create(string $route = 'level'): InertiaResponse|JsonResponse
    {
        $this->vars->formconfig = $this->vars->level->modelPath::getCrudConfig()['formconfig'];
        PbDebugbar::addMessage($this->vars->formconfig, 'formconfig');

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
            $model = $this->processModelRequests($this->vars->validationRules, $request, $this->vars->replacers,
                (new $this->vars->level->modelPath())->setLocale(app()->getLocale()));
            // Check if module relation exists
            if ($model->hasRelation('module') && $request->input('module') &&  $module = PbModule::find($request->input('module'))) {
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
     */
    public function show(
        int $id,
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): Application|RedirectResponse|Redirector|InertiaResponse|JsonResponse {

        $currentModel = $this->vars->level->modelPath::find($id);

        if (!$model = $this->buildModelsArray($element, $multiple, $id)) {
            return $this->redirectResponseCRUDFail(request(), 'show', "Error finding {$this->vars->level->name}");
        }
        if ($this->isUnreadableModel($currentModel)) {
            return $this->redirectResponseCRUDFail(request(), 'show', "This {$this->vars->level->name} cannot be shown");
        }
        if (!$currentModel->isViewableBy(Auth::user()->id)) {
            return $this->redirectResponseCRUDFail(request(), 'show', "You don't have permission to view this {$this->vars->level->name}");
        }

        return $this->renderResponse($this->buildRouteString($route, 'show'), $model);
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

        $this->vars->formconfig = $this->vars->level->modelPath::getCrudConfig()['formconfig'];
        PbDebugbar::addMessage($this->vars->formconfig, 'formconfig');

        $currentModel = $this->vars->level->modelPath::find($id);

        if (!$model = $this->buildModelsArray($element, $multiple, $id)) {
            return $this->redirectResponseCRUDFail(request(), 'edit', "Error finding {$this->vars->level->name}");
        }
        if ($this->isUnreadableModel($currentModel)) {
            return $this->redirectResponseCRUDFail(request(), 'edit', "This {$this->vars->level->name} cannot be modified");
        }
        if (!$currentModel->isEditableBy(Auth::user()->id)) {
            return $this->redirectResponseCRUDFail(request(), 'edit', "You don't have permission to edit this {$this->vars->level->name}");
        }

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
            // Check if module relation exists
            if ($model->hasRelation('module') && $request->input('module') &&  $module = PbModule::find($request->input('module'))) {
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
        if (isset($this->vars->level->modelPath::$enableable) && (new $this->vars->level->modelPath)->isEditableBy(Auth::user()->id)) {
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
        if (isset($this->vars->level->modelPath::$enableable) && (new $this->vars->level->modelPath)->isEditableBy(Auth::user()->id)) {
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
            $arrayElements[
            ($plural ? $this->vars->level->prefixNames : $this->vars->level->prefixName)] =
                ($id ? $this->vars->level->modelPath::find($id) : $this->buildPaginatedAndOrderedModel(null, $page, $perpage, $orderby, $field, $order));
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
            ...Shares::allowed($this->vars->allowed),
            ...Shares::list($this->vars->shares),
            ...['sort' => $this->vars->sortable],
            ...['showpos' => $this->vars->showPosition],
            ...['showid' => $this->vars->showId],
            ...['model' => $this->vars->viewModelName],
            ...['required' => $this->vars->required],
            ...['defaults' => $this->getDefaults()],
            ...['listing' => $this->vars->listing],
            ...['formconfig' => $this->vars->formconfig],
            ...['pagination' => $this->vars->pagination ?? ['location' => 'none']],
            ...['heading' => $this->vars->heading ?? ['location' => 'none']],
            ...['orderby' => $this->vars->orderby ?? []],
        ];
        Inertia::share('shared', $shared);
        PbDebugbar::addMessage($shared, 'shared');
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
    protected function processModelRequests(array $validationRules, Request $request, array $replacers, $model = null): array|object
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
        if (!PbHelpers::isApi($this->vars->request)) {
            //write your logic for web call
            $this->shareVars();
            Inertia::setRootView($this->vars->inertiaRoot);
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
        $object->keys = $this->vars->helper->class::toPlural($key);
        $object->name = strtolower($key);
        $object->names = $this->vars->helper->class::toPlural($object->name);
        $object->prefixName = strtolower($this->vars->helper->prefix . $key);
        $object->prefixNames = $this->vars->helper->class::toPlural($object->prefixName);
        $object->modelPath = $this->vars->helper->vendor . "\\" . $this->vars->helper->package . "\\Models\\" . $this->vars->helper->prefix . $key;
        $object->viewsPath = $this->vars->helper->package . "/" . $object->keys . "/";
        $object->table = (new $object->modelPath())->getTable();
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
                PbHelpers::getDefault('vendor') . '\\' . PbHelpers::getDefault('package') . '\\Helpers\\' . PbHelpers::getDefault('prefix') . 'Helpers'
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
        foreach($model->undeletableModels as $key => $value) {
            if (in_array($model->{$key}, $value)) {
                return true;
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
        foreach($model->unmodifiableModels as $key => $value) {
            if (in_array($model->{$key}, $value)) {
                return true;
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
        foreach($model->unreadableModels as $key => $value) {
            if (in_array($model->{$key}, $value)) {
                return true;
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
        foreach($model->unconfigurableModels as $key => $value) {
            if (in_array($model->{$key}, $value)) {
                return true;
            }
        }
        return false;
    }
}
