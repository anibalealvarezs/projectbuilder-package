<?php

namespace Anibalealvarezs\Projectbuilder\Controllers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;

use Auth;
use DB;
use Session;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PbBuilderController extends Controller
{
    use PbControllerTrait;

    /**
     * @var string
     */
    protected $vendor;
    /**
     * @var string
     */
    protected $package;
    /**
     * @var string
     */
    protected $helper;
    /**
     * @var string
     */
    protected $viewModelName;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $names;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var string
     */
    protected $prefix;
    /**
     * @var string
     */
    protected $key;
    /**
     * @var string
     */
    protected $keys;
    /**
     * @var string
     */
    protected $model;
    /**
     * @var string
     */
    protected $models;
    /**
     * @var string
     */
    protected $modelPath;
    /**
     * @var string
     */
    protected $viewsPath;
    /**
     * @var string
     */
    protected $prefixName;
    /**
     * @var string
     */
    protected $prefixNames;
    /**
     * @var string
     */
    protected $parentKey;
    /**
     * @var string
     */
    protected $parentKeys;
    /**
     * @var string
     */
    protected $parentModel;
    /**
     * @var string
     */
    protected $parentModels;
    /**
     * @var string
     */
    protected $parentName;
    /**
     * @var string
     */
    protected $parentNames;
    /**
     * @var string
     */
    protected $parentViewsPath;
    /**
     * @var string
     */
    protected $prefixParentName;
    /**
     * @var string
     */
    protected $prefixParentNames;
    /**
     * @var string
     */
    protected $grandparentKey;
    /**
     * @var string
     */
    protected $grandparentKeys;
    /**
     * @var string
     */
    protected $grandparentModel;
    /**
     * @var string
     */
    protected $grandparentModels;
    /**
     * @var string
     */
    protected $grandparentName;
    /**
     * @var string
     */
    protected $grandparentNames;
    /**
     * @var string
     */
    protected $grandparentViewsPath;
    /**
     * @var string
     */
    protected $prefixGrandparentName;
    /**
     * @var string
     */
    protected $prefixGrandparentNames;
    /**
     * @var string
     */
    protected $childKey;
    /**
     * @var string
     */
    protected $childKeys;
    /**
     * @var string
     */
    protected $childModel;
    /**
     * @var string
     */
    protected $childModels;
    /**
     * @var string
     */
    protected $childName;
    /**
     * @var string
     */
    protected $childNames;
    /**
     * @var string
     */
    protected $childViewsPath;
    /**
     * @var string
     */
    protected $prefixChildName;
    /**
     * @var string
     */
    protected $prefixChildNames;
    /**
     * @var string
     */
    protected $grandchildKey;
    /**
     * @var string
     */
    protected $grandchildKeys;
    /**
     * @var string
     */
    protected $grandchildModel;
    /**
     * @var string
     */
    protected $grandchildModels;
    /**
     * @var string
     */
    protected $grandchildName;
    /**
     * @var string
     */
    protected $grandchildNames;
    /**
     * @var string
     */
    protected $grandchildViewsPath;
    /**
     * @var string
     */
    protected $prefixGrandchildName;
    /**
     * @var string
     */
    protected $prefixGrandchildNames;
    /**
     * @var array
     */
    protected $validationRules = [];
    /**
     * @var array
     */
    protected $replacers = [];
    /**
     * @var array
     */
    protected $shares = [];
    /**
     * @var array
     */
    protected $allowed = [];
    /**
     * @var string
     */
    protected $sortable;
    /**
     * @var array
     */
    protected $sortingRef;
    /**
     * @var array
     */
    protected $showPosition;
    /**
     * @var array
     */
    protected $showId;
    /**
     * @var array
     */
    protected $inertiaRoot;
    /**
     * @var array
     */
    protected $defaults = [];
    /**
     * @var array
     */
    protected $required = [];

    function __construct($crud_perms = false)
    {
        if (!$this->key) {
            $this->key = 'Builder';
        }
        if (!$this->prefix) {
            $this->prefix = 'Pb';
        }
        if (!$this->helper) {
            $this->helper = PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\\Helpers\\'.$this->prefix.'Helpers';
        }
        if (!$this->inertiaRoot) {
            $this->inertiaRoot = $this->helper::PB_PACKAGE.'::app';
        }
        if (!$this->vendor) {
            $this->vendor = $this->helper::PB_VENDOR;
        }
        if (!$this->package) {
            $this->package = $this->helper::PB_PACKAGE;
        }
        if (!$this->sortable) {
            $this->sortable = false;
        }
        $this->keys = $this->helper::toPlural($this->key);
        $this->model = $this->prefix.$this->key;
        $this->models = $this->helper::toPlural($this->model);
        $this->name = strtolower($this->key);
        $this->names = $this->helper::toPlural($this->name);
        $this->prefixName = strtolower($this->prefix.$this->key);
        $this->prefixNames = $this->helper::toPlural($this->prefixName);
        $this->modelPath = $this->vendor."\\".$this->package."\\Models\\".$this->model;
        $this->viewsPath = $this->package . '/'.$this->keys.'/';
        $this->table = (new $this->modelPath())->getTable();
        // Additional Parent Model Variables
        if ($this->parentKey) {
            $this->parentKeys = $this->helper::toPlural($this->parentKey);
            $this->parentModel = $this->prefix.$this->parentKey;
            $this->parentModels = $this->helper::toPlural($this->parentModel);
            $this->parentName = strtolower($this->parentKey);
            $this->parentNames = $this->helper::toPlural($this->parentName);
            $this->prefixParentName = strtolower($this->prefix.$this->parentKey);
            $this->prefixParentNames = $this->helper::toPlural($this->prefixParentName);
            $this->parentViewsPath = $this->package . '/'.$this->parentKeys.'/';
        }
        // Additional Grand Parent Model Variables
        if ($this->grandparentKey) {
            $this->grandparentKeys = $this->helper::toPlural($this->grandparentKey);
            $this->grandparentModel = $this->prefix.$this->grandparentKey;
            $this->grandparentModels = $this->helper::toPlural($this->grandparentModel);
            $this->grandparentName = strtolower($this->grandparentKey);
            $this->grandparentNames = $this->helper::toPlural($this->grandparentName);
            $this->prefixGrandparentName = strtolower($this->prefix.$this->grandparentKey);
            $this->prefixGrandparentNames = $this->helper::toPlural($this->prefixGrandparentName);
            $this->grandparentViewsPath = $this->package . '/'.$this->grandparentKeys.'/';
        }
        // Additional Child Model Variables
        if ($this->childKey) {
            $this->childKeys = $this->helper::toPlural($this->childKey);
            $this->childModel = $this->prefix.$this->childKey;
            $this->childModels = $this->helper::toPlural($this->childModel);
            $this->childName = strtolower($this->childKey);
            $this->childNames = $this->helper::toPlural($this->childName);
            $this->prefixChildName = strtolower($this->prefix.$this->childKey);
            $this->prefixChildNames = $this->helper::toPlural($this->prefixChildName);
            $this->childViewsPath = $this->package . '/'.$this->childKeys.'/';
        }
        // Additional Grand Child Model Variables
        if ($this->grandchildKey) {
            $this->grandchildKeys = $this->helper::toPlural($this->grandchildKey);
            $this->grandchildModel = $this->prefix.$this->grandchildKey;
            $this->grandchildModels = $this->helper::toPlural($this->grandchildModel);
            $this->grandchildName = strtolower($this->grandchildKey);
            $this->grandchildNames = $this->helper::toPlural($this->grandchildName);
            $this->prefixGrandchildName = strtolower($this->prefix.$this->grandchildKey);
            $this->prefixGrandchildNames = $this->helper::toPlural($this->prefixGrandchildName);
            $this->grandchildViewsPath = $this->package . '/'.$this->grandchildKeys.'/';
        }

        if ($crud_perms) {
            // Middlewares
            $this->middleware(['role_or_permission:read '.$this->names]);
            $this->middleware(['role_or_permission:create '.$this->names])->only('create', 'store');
            $this->middleware(['role_or_permission:update '.$this->names])->only('edit', 'update');
            $this->middleware(['role_or_permission:delete '.$this->names])->only('destroy');
        }

        if (!$this->viewModelName) {
            $this->viewModelName = $this->names;
        }

        if (!$this->sortingRef) {
            $this->sortingRef = null;
        }

        if (!$this->showPosition) {
            $this->showPosition = false;
        }

        if (!$this->showId) {
            $this->showId = true;
        }

        $this->required = $this->getRequired();
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return void
     */
    public function index($element = null, bool $multiple = false, string $route = 'level')
    {
        $arrayElements = $this->buildModelsArray($element, $multiple, null, true);

        $this->allowed = [
            'create '.$this->names => 'create',
            'update '.$this->names => 'update',
            'delete '.$this->names => 'delete',
        ];

        $path = $this->buildRouteString($route, 'index');

        return $this->renderView($path, $arrayElements);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $route
     * @return InertiaResponse
     */
    public function create(string $route = 'level'): InertiaResponse
    {
        $path = $this->buildRouteString($route, 'create');

        return $this->renderView($path);
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

            return $this->redirectResponseCRUDSuccess($request, $this->key.' created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be created!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return Application|RedirectResponse|Redirector|InertiaResponse
     */
    public function show(int $id, $element = null, bool $multiple = false, string $route = 'level')
    {
        $arrayElements = $this->buildModelsArray($element, $multiple, $id);

        $path = $this->buildRouteString($route, 'show');

        return $this->renderView($path, $arrayElements);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse
     */
    public function edit(int $id, $element = null, bool $multiple = false, string $route = 'level'): InertiaResponse
    {
        $arrayElements = $this->buildModelsArray($element, $multiple, $id);

        $path = $this->buildRouteString($route, 'edit');

        return $this->renderView($path, $arrayElements);
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

            return $this->redirectResponseCRUDSuccess($request, $this->key.' updated successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be updated!');
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

            return $this->redirectResponseCRUDSuccess($request, $this->key.' deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be deleted!');
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
            foreach($sortList as $sortEl) {
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

            return $this->redirectResponseCRUDSuccess($request, $this->key.' sorted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be sorted!');
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
    protected function buildModelsArray($element = null, bool $multiple = false, $id = null, bool $plural = false): array
    {
        $arrayElements = [];
        if ($element) {
            if ($multiple) {
                foreach($element as $key => $value) {
                    switch($key) {
                        case 'child':
                            $arrayElements[($value['size'] == 'multiple' ? $this->prefixChildNames : $this->prefixChildName)] = $value['object'];
                            break;
                        case 'grandchild':
                            $arrayElements[($value['size'] == 'multiple' ? $this->prefixGrandchildNames : $this->prefixGrandchildName)] = $value['object'];
                            break;
                        case 'parent':
                            $arrayElements[($value['size'] == 'multiple' ? $this->prefixParentNames : $this->prefixParentName)] = $value['object'];
                            break;
                        case 'grandparent':
                            $arrayElements[($value['size'] == 'multiple' ? $this->prefixGrandparentNames : $this->prefixGrandparentName)] = $value['object'];
                            break;
                        default:
                            $arrayElements[($value['size'] == 'multiple' ? $this->prefixNames : $this->prefixName)] = $value['object'];
                            break;
                    }
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
        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::allowed($this->allowed),
                Shares::list($this->shares),
                ['sort' => $this->sortable],
                ['showpos' => $this->showPosition],
                ['showid' => $this->showId],
                ['model' => $this->viewModelName],
                ['required' => $this->required],
                ['defaults' => $this->getDefaults()],
            )
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    protected function getDefaults()
    {
        $defaults = (object) [];
        foreach($this->defaults as $key => $value) {
            switch($key) {
                case 'lang':
                    $defaults->$key = PbLanguage::findByCode($value);
                    break;
                case 'country':
                    $defaults->$key = PbCountry::findByCode($value);
                    break;
                default:
                    $defaults->$key = $value;
                    break;
            }
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
        foreach($this->validationRules as $key => $value) {
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
        foreach($validationRules as $vrKey => $vr) {
            if (isset($replacers[$vrKey])) {
                ${$replacers[$vrKey]} = $request[$vrKey];
                array_push($keys, $replacers[$vrKey]);
            } else {
                ${$vrKey} = $request[$vrKey];
                array_push($keys, $vrKey);
            }
        }
        if ($model) {
            foreach($keys as $key) {
                $model->$key = ${$key};
            }
            return $model;
        } else {
            $requests = [];
            foreach($keys as $key) {
                $requests[$key] = ${$key};
            }
            return $requests;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $view
     * @param array $elements
     * @return InertiaResponse
     */
    protected function renderView($view, array $elements = []): InertiaResponse
    {
        $this->shareVars();

        Inertia::setRootView($this->inertiaRoot);

        return Inertia::render($view, $elements);
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
        switch($route) {
            case 'child':
                $path = $this->childViewsPath.$this->buildFile($type, ['singular' => $this->childKey, 'plural' => $this->childKeys]);
                break;
            case 'grandchild':
                $path = $this->grandchildViewsPath.$this->buildFile($type, ['singular' => $this->grandchildKey, 'plural' => $this->grandchildKeys]);
                break;
            case 'parent':
                $path = $this->parentViewsPath.$this->buildFile($type, ['singular' => $this->parentKey, 'plural' => $this->parentKeys]);
                break;
            case 'grandparent':
                $path = $this->grandparentViewsPath.$this->buildFile($type, ['singular' => $this->grandparentKey, 'plural' => $this->grandparentKeys]);
                break;
            default:
                $path = $this->viewsPath.$this->buildFile($type, ['singular' => $this->key, 'plural' => $this->keys]);
                break;
        }

        return $path;
    }

    protected function buildFile($type, $keys)
    {
        switch($type) {
            case 'show':
                $file = 'Show'.$keys['singular'];
                break;
            case 'create':
                $file = 'Create'.$keys['singular'];
                break;
            case 'edit':
                $file = 'Edit'.$keys['singular'];
                break;
            default:
                $file = $keys['plural'];
                break;
        }

        return $file;
    }
}
