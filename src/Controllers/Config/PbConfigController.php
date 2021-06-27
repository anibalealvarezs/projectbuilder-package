<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Config;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Helpers\ControllerTrait;
use Anibalealvarezs\Projectbuilder\Models\PbConfig;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

    use ControllerTrait;

    function __construct()
    {
        $this->middleware(['role_or_permission:crud super-admin']);
        $this->aeas = new AeasHelpers();
        $this->name = "configs";
        $Config = new PbConfig();
        $this->table = $Config->getTable();
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
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:190'],
            'configkey' => ['required', 'max:50', Rule::unique($this->table)],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $current = "";
            foreach ($errors->all() as $message) {
                $current = $message;
            }
            $request->session()->flash('flash.banner', $current);
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->back()->withInput();
        } else {

            try {
                PbConfig::create($request->all());

                $request->session()->flash('flash.banner', 'Config Created Successfully!');
                $request->session()->flash('flash.bannerStyle', 'success');

                return redirect()->route($this->name.'.index');
            } catch (Exception $e) {
                $request->session()->flash('flash.banner', 'Config could not be created!');
                $request->session()->flash('flash.bannerStyle', 'danger');

                return redirect()->back()->withInput();
            }
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
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:190'],
            'configkey' => ['required', 'max:50', Rule::unique('config')->ignore($id)],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $current = "";
            foreach ($errors->all() as $message) {
                $current = $message;
            }
            $request->session()->flash('flash.banner', $current);
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->back()->withInput();
        } else {

            $config = PbConfig::find($id);
            try {
                $config->update($request->all());

                $request->session()->flash('flash.banner', 'Config Created Successfully!');
                $request->session()->flash('flash.bannerStyle', 'success');

                return redirect()->route($this->name.'.index');
            } catch (Exception $e) {
                $request->session()->flash('flash.banner', 'Config couldn\'t be updated!');
                $request->session()->flash('flash.bannerStyle', 'danger');

                return redirect()->back()->withInput();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $config = PbConfig::find($id);
        try {
            $config->delete();

            $request->session()->flash('flash.banner', 'Config deleted successfully!');
            $request->session()->flash('flash.bannerStyle', 'success');

            return redirect()->route($this->name.'.index');
        } catch (Exception $e) {
            $request->session()->flash('flash.banner', 'Config couldn\'t be deleted!');
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->back()->withInput();
        }
    }
}
