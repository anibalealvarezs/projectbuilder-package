<?php

namespace Anibalealvarezs\Projectbuilder\Controllers;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
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
    protected $name;
    protected $names;
    protected $table;
    protected $prefix;
    protected $key;
    protected $keys;
    protected $model;
    protected $models;
    protected $modelPath;
    protected $viewsPath;
    protected $prefixName;
    protected $prefixNames;
    protected $storeValidation;
    protected $updateValidation;

    use PbControllerTrait;

    function __construct()
    {
        if (!$this->key) {
            $this->key = 'Builder';
        }
        if (!$this->prefix) {
            $this->prefix = 'Pb';
        }
        $this->keys = AeasHelpers::toPlural($this->key);
        $this->model = $this->prefix.$this->key;
        $this->models = AeasHelpers::toPlural($this->model);
        $this->name = strtolower($this->key);
        $this->names = AeasHelpers::toPlural($this->name);
        $this->prefixName = strtolower($this->prefix.$this->key);
        $this->prefixNames = AeasHelpers::toPlural($this->prefixName);
        $this->modelPath = AeasHelpers::AEAS_VENDOR."\\".AeasHelpers::AEAS_PACKAGE."\\Models\\".$this->model;
        $this->viewsPath = AeasHelpers::AEAS_PACKAGE . '/'.$this->keys.'/';
        $this->storeValidation = [];
        $this->updateValidation = [];
        $this->table = (new $this->modelPath())->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $elements
     * @param array $shares
     * @return InertiaResponse
     */
    public function index($elements = null, array $shares = []): InertiaResponse
    {
        if ($elements) {
            ${$this->names} = $elements;
        } else {
            ${$this->names} = $this->modelPath::all();
        }

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::allowed([
                    'create '.$this->names => 'create',
                    'update '.$this->names => 'update',
                    'delete '.$this->names => 'delete',
                ]),
                Shares::list($shares),
            )
        );

        return Inertia::render($this->viewsPath.$this->keys, [
            $this->prefixNames => ${$this->names},
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param array $shares
     * @return InertiaResponse
     */
    public function create(array $shares = []): InertiaResponse
    {
        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
            ),
            Shares::list($shares),
        );

        return Inertia::render($this->viewsPath.'Create'.$this->key);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param array $validationRules
     * @param array $replacers
     * @return void
     */
    public function store(Request $request, array $validationRules = [], array $replacers = [])
    {
        $this->storeValidation = array_merge($this->storeValidation, $validationRules);

        // Validation
        $validator = Validator::make($request->all(), $this->storeValidation);
        $this->validationCheck($validator, $request);

        // Requests
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

        // Process
        try {
            ${$this->name} = new $this->modelPath();
            foreach($keys as $key) {
                ${$this->name}->$key = ${$key};
            }
            ${$this->name}->save();

            return $this->redirectResponseCRUDSuccess($request, $this->key.' created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be created!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param array $shares
     * @return InertiaResponse
     */
    public function show(int $id, array $shares = []): InertiaResponse
    {
        ${strtolower($this->key)} = $this->modelPath::find($id);

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
            ),
            Shares::list($shares),
        );

        return Inertia::render($this->viewsPath.'Show'.$this->key, [
            $this->prefixName => ${$this->name},
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param array $shares
     * @return InertiaResponse
     */
    public function edit(int $id, array $shares = []): InertiaResponse
    {
        ${$this->name} = $this->modelPath::find($id);

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
            ),
            Shares::list($shares),
        );

        return Inertia::render(AeasHelpers::AEAS_PACKAGE . '/'.$this->keys.'/Edit'.$this->key, [
            $this->prefixName => ${$this->name},
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @param array $validationRules
     * @param array $replacers
     * @return void
     */
    public function update(Request $request, int $id, array $validationRules = [], array $replacers = [])
    {
        $this->updateValidation = array_merge($this->updateValidation, $validationRules);

        // Validation
        $validator = Validator::make($request->all(), $this->updateValidation);
        $this->validationCheck($validator, $request);

        // Requests
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

        // Process
        try {
            ${$this->name} = $this->modelPath::find($id);
            $requests = [];
            foreach($keys as $key) {
                $requests[$key] = ${$key};
            }
            ${$this->name}->update($requests);

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
            ${$this->name} = $this->modelPath::find($id);
            ${$this->name}->delete();

            return $this->redirectResponseCRUDSuccess($request, $this->key.' deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, $this->key.' could not be deleted!');
        }
    }
}
