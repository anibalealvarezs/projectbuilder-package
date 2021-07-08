<?php

namespace Anibalealvarezs\Projectbuilder\Controllers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
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
    protected $prefixGrandchildName;
    /**
     * @var string
     */
    protected $prefixGrandchildNames;
    /**
     * @var array
     */
    protected $storeValidation = [];
    /**
     * @var array
     */
    protected $updateValidation = [];
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

    function __construct($crud_perms = false)
    {
        if (!$this->vendor) {
            $this->vendor = PbHelpers::PB_VENDOR;
        }
        if (!$this->package) {
            $this->package = PbHelpers::PB_PACKAGE;
        }
        if (!$this->key) {
            $this->key = 'Builder';
        }
        if (!$this->prefix) {
            $this->prefix = 'Pb';
        }
        $this->keys = PbHelpers::toPlural($this->key);
        $this->model = $this->prefix.$this->key;
        $this->models = PbHelpers::toPlural($this->model);
        $this->name = strtolower($this->key);
        $this->names = PbHelpers::toPlural($this->name);
        $this->prefixName = strtolower($this->prefix.$this->key);
        $this->prefixNames = PbHelpers::toPlural($this->prefixName);
        $this->modelPath = $this->vendor."\\".$this->package."\\Models\\".$this->model;
        $this->viewsPath = $this->package . '/'.$this->keys.'/';
        $this->table = (new $this->modelPath())->getTable();
        // Additional Parent Model Variables
        if ($this->parentKey) {
            $this->parentKeys = PbHelpers::toPlural($this->parentKey);
            $this->parentModel = $this->prefix.$this->parentKey;
            $this->parentModels = PbHelpers::toPlural($this->parentModel);
            $this->parentName = strtolower($this->parentKey);
            $this->parentNames = PbHelpers::toPlural($this->parentName);
            $this->prefixParentName = strtolower($this->prefix.$this->parentKey);
            $this->prefixParentNames = PbHelpers::toPlural($this->prefixParentName);
        }
        // Additional Grand Parent Model Variables
        if ($this->grandparentKey) {
            $this->grandparentKeys = PbHelpers::toPlural($this->grandparentKey);
            $this->grandparentModel = $this->prefix.$this->grandparentKey;
            $this->grandparentModels = PbHelpers::toPlural($this->grandparentModel);
            $this->grandparentName = strtolower($this->grandparentKey);
            $this->grandparentNames = PbHelpers::toPlural($this->grandparentName);
            $this->prefixGrandparentName = strtolower($this->prefix.$this->grandparentKey);
            $this->prefixGrandparentNames = PbHelpers::toPlural($this->prefixGrandparentName);
        }
        // Additional Child Model Variables
        if ($this->childKey) {
            $this->childKeys = PbHelpers::toPlural($this->childKey);
            $this->childModel = $this->prefix.$this->childKey;
            $this->childModels = PbHelpers::toPlural($this->childModel);
            $this->childName = strtolower($this->childKey);
            $this->childNames = PbHelpers::toPlural($this->childName);
            $this->prefixChildName = strtolower($this->prefix.$this->childKey);
            $this->prefixChildNames = PbHelpers::toPlural($this->prefixChildName);
        }
        // Additional Grand Child Model Variables
        if ($this->grandchildKey) {
            $this->grandchildKeys = PbHelpers::toPlural($this->grandchildKey);
            $this->grandchildModel = $this->prefix.$this->grandchildKey;
            $this->grandchildModels = PbHelpers::toPlural($this->grandchildModel);
            $this->grandchildName = strtolower($this->grandchildKey);
            $this->grandchildNames = PbHelpers::toPlural($this->grandchildName);
            $this->prefixGrandchildName = strtolower($this->prefix.$this->grandchildKey);
            $this->prefixGrandchildNames = PbHelpers::toPlural($this->prefixGrandchildName);
        }

        if ($crud_perms) {
            // Middlewares
            $this->middleware(['role_or_permission:read '.$this->names]);
            $this->middleware(['role_or_permission:create '.$this->names])->only('create', 'store');
            $this->middleware(['role_or_permission:update '.$this->names])->only('edit', 'update');
            $this->middleware(['role_or_permission:delete '.$this->names])->only('destroy');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $element
     * @param bool $multiple
     * @return InertiaResponse
     */
    public function index($element = null, bool $multiple = false): InertiaResponse
    {
        $arrayElements = $this->buildModelsArray($element, $multiple, null, true);

        $this->allowed = [
            'create '.$this->names => 'create',
            'update '.$this->names => 'update',
            'delete '.$this->names => 'delete',
        ];

        return $this->renderView($this->viewsPath.$this->keys, $arrayElements);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        return $this->renderView($this->viewsPath.'Create'.$this->key);
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
        $this->validateRequest('store', $this->validationRules, $request);

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
     * @return InertiaResponse
     */
    public function show(int $id, $element = null, bool $multiple = false): InertiaResponse
    {
        $arrayElements = $this->buildModelsArray($element, $multiple, $id);

        return $this->renderView($this->viewsPath.'Show'.$this->key, $arrayElements);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param null $element
     * @param bool $multiple
     * @return InertiaResponse
     */
    public function edit(int $id, $element = null, bool $multiple = false): InertiaResponse
    {
        $arrayElements = $this->buildModelsArray($element, $multiple, $id);

        return $this->renderView($this->viewsPath.'Edit'.$this->key, $arrayElements);
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
        $this->validateRequest('update', $this->validationRules, $request);

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
                            $arrayElements[$this->prefixChildName] = $value;
                            break;
                        case 'grandchild':
                            $arrayElements[$this->prefixGrandchildName] = $value;
                            break;
                        case 'parent':
                            $arrayElements[$this->prefixParentName] = $value;
                            break;
                        case 'grandparent':
                            $arrayElements[$this->prefixGrandparentName] = $value;
                            break;
                        default:
                            $arrayElements[$this->prefixName] = $value;
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
            )
        );
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
     * @param $method
     * @param $validationRules
     * @param Request $request
     * @return void
     */
    protected function validateRequest($method, $validationRules, Request $request)
    {
        $key = $method.'Validation';
        $this->$key = array_merge($this->$key, $validationRules);
        $validator = Validator::make($request->all(), $this->$key);
        $this->validationCheck($validator, $request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $view
     * @param array $elements
     * @return void
     */
    protected function renderView($view, array $elements = [])
    {
        $this->shareVars();

        return Inertia::render($view, $elements);
    }
}
