<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Config;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;
use Anibalealvarezs\Projectbuilder\Models\PbConfig;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Session;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PbConfigController extends Controller
{
    protected $aeas;
    protected $name;
    protected $table;

    use PbControllerTrait;

    function __construct()
    {
        // Middlewares
        $this->middleware(['role_or_permission:read configs']);
        $this->middleware(['role_or_permission:create configs'])->only('create', 'store');
        $this->middleware(['role_or_permission:update configs'])->only('edit', 'update');
        $this->middleware(['role_or_permission:delete configs'])->only('destroy');
        // Variables
        $this->aeas = new AeasHelpers();
        $this->name = "configs";
        $this->table = (new PbConfig())->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $configs = PbConfig::all();

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::allowed([
                    'create configs' => 'create',
                    'update configs' => 'update',
                    'delete configs' => 'delete',
                ]),
            )
        );

        return Inertia::render($this->aeas->package . '/Configs/Configs', [
            'pbconfigs' => $configs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
            )
        );

        return Inertia::render($this->aeas->package . '/Configs/CreateConfig');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @throws ValidationException
     * @return void
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:190'],
            'configkey' => ['required', 'max:50', Rule::unique($this->table)],
        ]);
        $this->validationCheck($validator, $request);

        // Process
        try {
            PbConfig::create($request->all());

            return $this->redirectResponseCRUDSuccess($request, 'Config created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'Config could not be created!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return InertiaResponse
     */
    public function show(int $id): InertiaResponse
    {
        $config = PbConfig::find($id);

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
            )
        );

        return Inertia::render($this->aeas->package . '/Configs/ShowConfig', [
            'pbconfig' => $config,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return InertiaResponse
     */
    public function edit(int $id): InertiaResponse
    {
        $config = PbConfig::find($id);

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
            )
        );

        return Inertia::render($this->aeas->package . '/Configs/EditConfig', [
            'pbconfig' => $config,
        ]);
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
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:190'],
            'configkey' => ['required', 'max:50', Rule::unique('config')->ignore($id)],
        ]);
        $this->validationCheck($validator, $request);

        // Process
        try {
            $config = PbConfig::find($id);
            $config->update($request->all());

            return $this->redirectResponseCRUDSuccess($request, 'Config updated successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'Config could not be updated!');
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
            $config = PbConfig::find($id);
            $config->delete();

            return $this->redirectResponseCRUDSuccess($request, 'Config deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'Config could not be deleted!');
        }
    }
}
