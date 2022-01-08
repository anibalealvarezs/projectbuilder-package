<?php

namespace Anibalealvarezs\Projectbuilder\Controllers;

use Anibalealvarezs\Projectbuilder\Helpers\PbDebugbar;
use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
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
     * @var string
     */
    protected string $vendor = "";
    /**
     * @var string
     */
    protected string $package = "";
    /**
     * @var string
     */
    protected string $helper = "";
    /**
     * @var string
     */
    protected string $viewModelName = "";
    /**
     * @var string
     */
    protected string $prefix = "";
    /**
     * @var array
     */
    protected array $keys = [];
    /**
     * @var array
     */
    protected array $validationRules = [];
    /**
     * @var array
     */
    protected array $modelExclude = [];
    /**
     * @var array
     */
    protected array $replacers = [];
    /**
     * @var array
     */
    protected array $shares = [];
    /**
     * @var array
     */
    protected array $allowed = [];
    /**
     * @var boolean
     */
    protected bool $sortable = false;
    /**
     * @var string
     */
    protected string $sortingRef = "";
    /**
     * @var boolean
     */
    protected bool $showPosition = false;
    /**
     * @var boolean
     */
    protected bool $showId = true;
    /**
     * @var string
     */
    protected string $inertiaRoot = "";
    /**
     * @var array
     */
    protected array $defaults = [];
    /**
     * @var array
     */
    protected array $required = [];
    /**
     * @var Request
     */
    protected Request $request;
    /**
     * @var array
     */
    protected array $listing = [];
    /**
     * @var array
     */
    protected array $formconfig = [];
    /**
     * @var object
     */
    protected object $controllerVars;

    function __construct(Request $request, $crud_perms = false)
    {
        if (!isset($this->controllerVars)) {
            $this->controllerVars = (object)[];
        }
        if (!isset($this->keys['level'])) {
            $this->keys['level'] = 'Builder';
        }
        if (!$this->prefix) {
            $this->prefix = PbHelpers::getDefault('prefix');
        }
        if (!$this->vendor) {
            $this->vendor = PbHelpers::getDefault('vendor');
        }
        if (!$this->package) {
            $this->package = PbHelpers::getDefault('package');
        }
        if (!$this->helper) {
            $this->helper = $this->vendor . '\\' . $this->package . '\\Helpers\\' . $this->prefix . 'Helpers';
        }
        if (!$this->inertiaRoot) {
            $this->inertiaRoot = $this->package . '::app';
        }

        foreach ($this->helper::getModelsLevels() as $value) {
            if (isset($this->keys[$value])) {
                $this->controllerVars->{$value} = $this->buildControllerVars($this->keys[$value]);
            }
        }

        if ($crud_perms) {
            // Middlewares
            foreach($this->helper::getMethodsByPermission() as $key => $value) {
                $this->middleware('role_or_permission:' . $key . ' ' . $this->controllerVars->level->names, $value);
            }
        }

        if (!$this->viewModelName) {
            $this->viewModelName = $this->controllerVars->level->names;
        }

        $this->required = $this->getRequired();

        $this->request = $request;

        self::$item = $this->controllerVars->level->name;
        self::$route = $this->controllerVars->level->names;
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse|RedirectResponse
     */
    public function index(
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): InertiaResponse|JsonResponse|RedirectResponse {
        $arrayElements = $this->buildModelsArray($element, $multiple, null, true);

        $this->allowed = [
            'create ' . $this->controllerVars->level->names => 'create',
            'update ' . $this->controllerVars->level->names => 'update',
            'delete ' . $this->controllerVars->level->names => 'delete',
        ];

        $config = $this->controllerVars->level->modelPath::getCrudConfig();
        if (!isset($config['fields']['actions'])) {
            $config['fields']['actions'] = [
                'update' => [],
                'delete' => []
            ];
        }
        $config['enabled_actions'] = Shares::allowed($this->allowed)['allowed'];
        if (!isset($config['model'])) {
            $config['model'] = $this->controllerVars->level->modelPath;
        }

        $this->listing = self::buildListingRow($config);
        $this->formconfig = $config['formconfig'];
        PbDebugbar::addMessage($this->listing, 'listing');
        PbDebugbar::addMessage($this->formconfig, 'formconfig');

        $path = $this->buildRouteString($route, 'index');

        return $this->renderResponse($path, $arrayElements);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $route
     * @return InertiaResponse|JsonResponse
     */
    public function create(string $route = 'level'): InertiaResponse|JsonResponse
    {
        $config = $this->controllerVars->level->modelPath::getCrudConfig();
        $this->formconfig = $config['formconfig'];
        PbDebugbar::addMessage($this->formconfig, 'formconfig');

        $path = $this->buildRouteString($route, 'create');

        return $this->renderResponse($path);
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
        if ($failed = $this->validateRequest($this->validationRules, $request)) {
            return $failed;
        }

        // Process
        try {
            // Add requests
            $model = $this->processModelRequests($this->validationRules, $request, $this->replacers,
                new $this->controllerVars->level->modelPath());
            // Model save
            $model->save();

            return $this->redirectResponseCRUDSuccess($request,
                $this->controllerVars->level->key . ' created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request,
                $this->controllerVars->level->key . ' could not be created! ' . $e->getMessage());
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
        $arrayElements = $this->buildModelsArray($element, $multiple, $id);

        $path = $this->buildRouteString($route, 'show');

        return $this->renderResponse($path, $arrayElements);
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
        $arrayElements = $this->buildModelsArray($element, $multiple, $id);

        $config = $this->controllerVars->level->modelPath::getCrudConfig();
        $this->formconfig = $config['formconfig'];
        PbDebugbar::addMessage($this->formconfig, 'formconfig');

        $path = $this->buildRouteString($route, 'edit');

        return $this->renderResponse($path, $arrayElements);
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
        if ($failed = $this->validateRequest($this->validationRules, $request)) {
            return $failed;
        }

        // Process
        try {
            // Build model
            $model = $this->controllerVars->level->modelPath::find($id);
            // Build requests
            $requests = $this->processModelRequests($this->validationRules, $request, $this->replacers);
            // Update model
            $model->update($requests);

            return $this->redirectResponseCRUDSuccess($request,
                $this->controllerVars->level->key . ' updated successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request,
                $this->controllerVars->level->key . ' could not be updated! ' . $e->getMessage());
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
            // Delete element
            $this->controllerVars->level->modelPath::find($id)->delete();

            return $this->redirectResponseCRUDSuccess($request,
                $this->controllerVars->level->key . ' deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request,
                $this->controllerVars->level->key . ' could not be deleted! ' . $e->getMessage());
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
                    $query = $this->controllerVars->level->modelPath::where($this->sortingRef, $id)
                        ->where('id', $sortEl)->first();
                    if ($query) {
                        // Build model
                        $el = $this->controllerVars->level->modelPath::find($query->id);
                    }
                } else {
                    // Build model
                    $el = $this->controllerVars->level->modelPath::find($sortEl);
                }
                if ($el) {
                    $el->position = $n;
                    $el->save();
                }
                $n++;
            }

            return $this->redirectResponseCRUDSuccess($request,
                $this->controllerVars->level->key . ' sorted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request,
                $this->controllerVars->level->key . ' could not be sorted! ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param null $element
     * @param bool $multiple
     * @param null $id
     * @param bool $plural
     * @return array
     */
    protected function buildModelsArray(
        $element = null,
        bool $multiple = false,
        $id = null,
        bool $plural = false
    ): array {
        $arrayElements = [];
        if ($element) {
            if ($multiple) {
                foreach ($element as $key => $value) {
                    $arrayElements[($value['size'] == 'multiple' ? $this->controllerVars->{$key}->prefixNames : $this->controllerVars->{$key}->prefixName)] = $value['object'];
                }
            } else {
                $arrayElements[($plural ? $this->controllerVars->level->prefixNames : $this->controllerVars->level->prefixName)] = $element;
            }
        } else {
            $arrayElements[($plural ? $this->controllerVars->level->prefixNames : $this->controllerVars->level->prefixName)] = ($id ? $this->controllerVars->level->modelPath::find($id) : $this->controllerVars->level->modelPath::all());
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
            ...Shares::allowed($this->allowed),
            ...Shares::list($this->shares),
            ...['sort' => $this->sortable],
            ...['showpos' => $this->showPosition],
            ...['showid' => $this->showId],
            ...['model' => $this->viewModelName],
            ...['required' => $this->required],
            ...['defaults' => $this->getDefaults()],
            ...['listing' => $this->listing],
            ...['formconfig' => $this->formconfig],
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
        foreach ($this->defaults as $key => $value) {
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
        foreach ($this->validationRules as $key => $value) {
            if (in_array('required', $value)) {
                array_push($required, $key);
            }
        }
        return $required;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $validationRules
     * @param Request $request
     * @param $replacers
     * @param null $model
     * @return void
     */
    protected function processModelRequests($validationRules, Request $request, $replacers, $model = null)
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
        if ($model) {
            foreach ($keys as $key) {
                if (!in_array($key, $this->modelExclude)) {
                    $model->$key = ${$key};
                }
            }
            return $model;
        } else {
            $requests = [];
            foreach ($keys as $key) {
                if (!in_array($key, $this->modelExclude)) {
                    $requests[$key] = ${$key};
                }
            }
            return $requests;
        }
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
        if ($this->request->is('api/*')) {
            //write your logic for api call
            if ($elements || $nullable) {
                return $this->handleJSONResponse($elements, 'Operation Successful');
            } else {
                return $this->handleJSONError('Operation Failed');
            }
        } else {
            //write your logic for web call
            $this->shareVars();

            Inertia::setRootView($this->inertiaRoot);

            return Inertia::render($view, $elements);
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
        return $this->controllerVars->{$route}->viewsPath .
            $this->buildFile(
                $type,
                ['singular' => $this->controllerVars->{$route}->key, 'plural' => $this->controllerVars->{$route}->keys]
            );
    }

    protected function buildFile($type, $keys)
    {
        return match ($type) {
            'show' => 'Show' . $keys['singular'],
            'create' => 'Create' . $keys['singular'],
            'edit' => 'Edit' . $keys['singular'],
            default => $keys['plural'],
        };
    }

    public function handleJSONResponse($result, $msg)
    {
        $res = [
            'success' => true,
            'data' => $result,
            'message' => $msg,
        ];
        return response()->json($res);
    }

    public function handleJSONError($error, $errorMsg = [], $code = 404)
    {
        $res = [
            'success' => false,
            'message' => $error,
        ];
        if (!empty($errorMsg)) {
            $res['data'] = $errorMsg;
        }
        return response()->json($res, $code);
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $level
     * @return object
     */
    public function buildControllerVars($key): object
    {
        $object = (object) [];
        $object->key = $key;
        $object->keys = $this->helper::toPlural($key);
        $object->model = $this->prefix . $key;
        $object->models = $this->helper::toPlural($object->model);
        $object->name = strtolower($key);
        $object->names = $this->helper::toPlural($object->name);
        $object->prefixName = strtolower($this->prefix . $key);
        $object->prefixNames = $this->helper::toPlural($object->prefixName);
        $object->modelPath = $this->vendor . "\\" . $this->package . "\\Models\\" . $object->model;
        $object->viewsPath = $this->package . "/" . $object->keys . "/";
        $object->table = (new $object->modelPath())->getTable();
        return $object;
    }
}
