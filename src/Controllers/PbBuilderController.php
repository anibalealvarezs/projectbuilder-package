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
    protected string $vendor;
    /**
     * @var string
     */
    protected string $package;
    /**
     * @var string
     */
    protected string $helper;
    /**
     * @var string
     */
    protected string $viewModelName;
    /**
     * @var string
     */
    protected string $name;
    /**
     * @var string
     */
    protected string $names;
    /**
     * @var string
     */
    protected string $table;
    /**
     * @var string
     */
    protected string $prefix;
    /**
     * @var string
     */
    protected string $key;
    /**
     * @var string
     */
    protected string $keys;
    /**
     * @var string
     */
    protected string $model;
    /**
     * @var string
     */
    protected string $models;
    /**
     * @var string
     */
    protected string $modelPath;
    /**
     * @var string
     */
    protected string $viewsPath;
    /**
     * @var string
     */
    protected string $prefixName;
    /**
     * @var string
     */
    protected string $prefixNames;
    /**
     * @var string
     */
    protected string $parentKey;
    /**
     * @var string
     */
    protected string $parentKeys;
    /**
     * @var string
     */
    protected string $parentModel;
    /**
     * @var string
     */
    protected string $parentModels;
    /**
     * @var string
     */
    protected string $parentName;
    /**
     * @var string
     */
    protected string $parentNames;
    /**
     * @var string
     */
    protected string $parentViewsPath;
    /**
     * @var string
     */
    protected string $prefixParentName;
    /**
     * @var string
     */
    protected string $prefixParentNames;
    /**
     * @var string
     */
    protected string $grandparentKey;
    /**
     * @var string
     */
    protected string $grandparentKeys;
    /**
     * @var string
     */
    protected string $grandparentModel;
    /**
     * @var string
     */
    protected string $grandparentModels;
    /**
     * @var string
     */
    protected string $grandparentName;
    /**
     * @var string
     */
    protected string $grandparentNames;
    /**
     * @var string
     */
    protected string $grandparentViewsPath;
    /**
     * @var string
     */
    protected string $prefixGrandparentName;
    /**
     * @var string
     */
    protected string $prefixGrandparentNames;
    /**
     * @var string
     */
    protected string $childKey;
    /**
     * @var string
     */
    protected string $childKeys;
    /**
     * @var string
     */
    protected string $childModel;
    /**
     * @var string
     */
    protected string $childModels;
    /**
     * @var string
     */
    protected string $childName;
    /**
     * @var string
     */
    protected string $childNames;
    /**
     * @var string
     */
    protected string $childViewsPath;
    /**
     * @var string
     */
    protected string $prefixChildName;
    /**
     * @var string
     */
    protected string $prefixChildNames;
    /**
     * @var string
     */
    protected string $grandchildKey;
    /**
     * @var string
     */
    protected string $grandchildKeys;
    /**
     * @var string
     */
    protected string $grandchildModel;
    /**
     * @var string
     */
    protected string $grandchildModels;
    /**
     * @var string
     */
    protected string $grandchildName;
    /**
     * @var string
     */
    protected string $grandchildNames;
    /**
     * @var string
     */
    protected string $grandchildViewsPath;
    /**
     * @var string
     */
    protected string $prefixGrandchildName;
    /**
     * @var string
     */
    protected string $prefixGrandchildNames;
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
    protected bool $sortable;
    /**
     * @var string
     */
    protected string $sortingRef;
    /**
     * @var boolean
     */
    protected bool $showPosition;
    /**
     * @var boolean
     */
    protected bool $showId;
    /**
     * @var string
     */
    protected string $inertiaRoot;
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
    protected array $listing;
    /**
     * @var array
     */
    protected array $formconfig;

    function __construct(Request $request, $crud_perms = false)
    {
        if (!isset($this->key)) {
            $this->key = 'Builder';
        }
        if (!isset($this->prefix)) {
            $this->prefix = 'Pb';
        }
        if (!isset($this->helper)) {
            $this->helper = PbHelpers::PB_VENDOR . '\\' . PbHelpers::PB_PACKAGE . '\\Helpers\\' . $this->prefix . 'Helpers';
        }
        if (!isset($this->inertiaRoot)) {
            $this->inertiaRoot = $this->helper::PB_PACKAGE . '::app';
        }
        if (!isset($this->vendor)) {
            $this->vendor = $this->helper::PB_VENDOR;
        }
        if (!isset($this->package)) {
            $this->package = $this->helper::PB_PACKAGE;
        }
        if (!isset($this->sortable)) {
            $this->sortable = false;
        }
        $this->keys = $this->helper::toPlural($this->key);
        $this->model = $this->prefix . $this->key;
        $this->models = $this->helper::toPlural($this->model);
        $this->name = strtolower($this->key);
        $this->names = $this->helper::toPlural($this->name);
        $this->prefixName = strtolower($this->prefix . $this->key);
        $this->prefixNames = $this->helper::toPlural($this->prefixName);
        $this->modelPath = $this->vendor . "\\" . $this->package . "\\Models\\" . $this->model;
        $this->viewsPath = $this->package . "/" . $this->keys . "/";
        $this->table = (new $this->modelPath())->getTable();
        // Additional Parent Model Variables
        if (isset($this->parentKey)) {
            $this->parentKeys = $this->helper::toPlural($this->parentKey);
            $this->parentModel = $this->prefix . $this->parentKey;
            $this->parentModels = $this->helper::toPlural($this->parentModel);
            $this->parentName = strtolower($this->parentKey);
            $this->parentNames = $this->helper::toPlural($this->parentName);
            $this->prefixParentName = strtolower($this->prefix . $this->parentKey);
            $this->prefixParentNames = $this->helper::toPlural($this->prefixParentName);
            $this->parentViewsPath = $this->package . "/" . $this->parentKeys . "/";
        }
        // Additional Grand Parent Model Variables
        if (isset($this->grandparentKey)) {
            $this->grandparentKeys = $this->helper::toPlural($this->grandparentKey);
            $this->grandparentModel = $this->prefix . $this->grandparentKey;
            $this->grandparentModels = $this->helper::toPlural($this->grandparentModel);
            $this->grandparentName = strtolower($this->grandparentKey);
            $this->grandparentNames = $this->helper::toPlural($this->grandparentName);
            $this->prefixGrandparentName = strtolower($this->prefix . $this->grandparentKey);
            $this->prefixGrandparentNames = $this->helper::toPlural($this->prefixGrandparentName);
            $this->grandparentViewsPath = $this->package . "/" . $this->grandparentKeys . "/";
        }
        // Additional Child Model Variables
        if (isset($this->childKey)) {
            $this->childKeys = $this->helper::toPlural($this->childKey);
            $this->childModel = $this->prefix . $this->childKey;
            $this->childModels = $this->helper::toPlural($this->childModel);
            $this->childName = strtolower($this->childKey);
            $this->childNames = $this->helper::toPlural($this->childName);
            $this->prefixChildName = strtolower($this->prefix . $this->childKey);
            $this->prefixChildNames = $this->helper::toPlural($this->prefixChildName);
            $this->childViewsPath = $this->package . "/" . $this->childKeys . "/";
        }
        // Additional Grand Child Model Variables
        if (isset($this->grandchildKey)) {
            $this->grandchildKeys = $this->helper::toPlural($this->grandchildKey);
            $this->grandchildModel = $this->prefix . $this->grandchildKey;
            $this->grandchildModels = $this->helper::toPlural($this->grandchildModel);
            $this->grandchildName = strtolower($this->grandchildKey);
            $this->grandchildNames = $this->helper::toPlural($this->grandchildName);
            $this->prefixGrandchildName = strtolower($this->prefix . $this->grandchildKey);
            $this->prefixGrandchildNames = $this->helper::toPlural($this->prefixGrandchildName);
            $this->grandchildViewsPath = $this->package . "/" . $this->grandchildKeys . "/";
        }

        if ($crud_perms) {
            // Middlewares
            $this->middleware(['role_or_permission:read ' . $this->names]);
            $this->middleware(['role_or_permission:create ' . $this->names])->only('create', 'store');
            $this->middleware(['role_or_permission:update ' . $this->names])->only('edit', 'update');
            $this->middleware(['role_or_permission:delete ' . $this->names])->only('destroy');
        }

        if (!isset($this->viewModelName)) {
            $this->viewModelName = $this->names;
        }

        if (!isset($this->sortingRef)) {
            $this->sortingRef = "";
        }

        if (!isset($this->showPosition)) {
            $this->showPosition = false;
        }

        if (!isset($this->showId)) {
            $this->showId = true;
        }

        if (!isset($this->listing)) {
            $this->listing = [];
        }

        if (!isset($this->formconfig)) {
            $this->formconfig = [];
        }

        $this->required = $this->getRequired();

        $this->request = $request;

        self::$item = $this->name;
        self::$route = $this->names;
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
            'create ' . $this->names => 'create',
            'update ' . $this->names => 'update',
            'delete ' . $this->names => 'delete',
        ];

        $config = $this->modelPath::getCrudConfig();
        if (!isset($config['fields']['actions'])) {
            $config['fields']['actions'] = [
                'update' => [],
                'delete' => []
            ];
        }
        $config['enabled_actions'] = Shares::allowed($this->allowed)['allowed'];
        if (!isset($config['model'])) {
            $config['model'] = $this->modelPath;
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
        $config = $this->modelPath::getCrudConfig();
        $this->formconfig = $config['formconfig'];
        PbDebugbar::addMessage($this->formconfig, 'formconfig');

        $path = $this->buildRouteString($route, 'create');

        return $this->renderResponse($path);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        // Validation
        if ($failed = $this->validateRequest($this->validationRules, $request)) {
            return $failed;
        }

        // Process
        try {
            // Build model
            $model = new $this->modelPath();
            // Add requests
            $model = $this->processModelRequests($this->validationRules, $request, $this->replacers, $model);
            // Model save
            $model->save();

            return $this->redirectResponseCRUDSuccess($request, $this->key . ' created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key . ' could not be created! '.$e->getMessage());
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

        $config = $this->modelPath::getCrudConfig();
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
     * @return void
     */
    public function update(Request $request, int $id)
    {
        // Validation
        if ($failed = $this->validateRequest($this->validationRules, $request)) {
            return $failed;
        }

        // Process
        try {
            // Build model
            $model = $this->modelPath::find($id);
            // Build requests
            $requests = $this->processModelRequests($this->validationRules, $request, $this->replacers);
            // Update model
            $model->update($requests);

            return $this->redirectResponseCRUDSuccess($request, $this->key . ' updated successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key . ' could not be updated! '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function destroy(Request $request, int $id)
    {
        // Process
        try {
            // Delete element
            $this->modelPath::find($id)->delete();

            return $this->redirectResponseCRUDSuccess($request, $this->key . ' deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key . ' could not be deleted! '.$e->getMessage());
        }
    }

    /**
     * Sort model elements
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function sort(Request $request, int $id)
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
                    $query = $this->modelPath::where($this->sortingRef, $id)->where('id', $sortEl)->first();
                    if ($query) {
                        // Build model
                        $el = $this->modelPath::find($query->id);
                    }
                } else {
                    // Build model
                    $el = $this->modelPath::find($sortEl);
                }
                if ($el) {
                    $el->position = $n;
                    $el->save();
                }
                $n++;
            }

            return $this->redirectResponseCRUDSuccess($request, $this->key . ' sorted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key . ' could not be sorted! '.$e->getMessage());
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
                    $key = match ($key) {
                        'child' => ($value['size'] == 'multiple' ? $this->prefixChildNames : $this->prefixChildName),
                        'grandchild' => ($value['size'] == 'multiple' ? $this->prefixGrandchildNames : $this->prefixGrandchildName),
                        'parent' => ($value['size'] == 'multiple' ? $this->prefixParentNames : $this->prefixParentName),
                        'grandparent' => ($value['size'] == 'multiple' ? $this->prefixGrandparentNames : $this->prefixGrandparentName),
                        default => ($value['size'] == 'multiple' ? $this->prefixNames : $this->prefixName),
                    };
                    $arrayElements[$key] = $value['object'];
                }
            } else {
                $arrayElements[($plural ? $this->prefixNames : $this->prefixName)] = $element;
            }
        } else {
            $arrayElements[($plural ? $this->prefixNames : $this->prefixName)] = ($id ? $this->modelPath::find($id) : $this->modelPath::all());
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
        $shared = array_merge(
            $this->globalInertiaShare(),
            Shares::allowed($this->allowed),
            Shares::list($this->shares),
            ['sort' => $this->sortable],
            ['showpos' => $this->showPosition],
            ['showid' => $this->showId],
            ['model' => $this->viewModelName],
            ['required' => $this->required],
            ['defaults' => $this->getDefaults()],
            ['listing' => $this->listing],
            ['formconfig' => $this->formconfig],
        );
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
        return match ($route) {
            'child' => $this->childViewsPath . $this->buildFile($type,
                    ['singular' => $this->childKey, 'plural' => $this->childKeys]),
            'grandchild' => $this->grandchildViewsPath . $this->buildFile($type,
                    ['singular' => $this->grandchildKey, 'plural' => $this->grandchildKeys]),
            'parent' => $this->parentViewsPath . $this->buildFile($type,
                    ['singular' => $this->parentKey, 'plural' => $this->parentKeys]),
            'grandparent' => $this->grandparentViewsPath . $this->buildFile($type,
                    ['singular' => $this->grandparentKey, 'plural' => $this->grandparentKeys]),
            default => $this->viewsPath . $this->buildFile($type, ['singular' => $this->key, 'plural' => $this->keys]),
        };
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
}
