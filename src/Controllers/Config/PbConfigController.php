<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Config;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbConfig;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PbConfigController extends Controller
{
    protected $aeas;

    function __construct()
    {
        $this->aeas = new AeasHelpers();
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $configs = PbConfig::all();

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
        Validator::make($request->all(), [
            'name' => ['required', 'max:190'],
            'configkey' => ['required', 'max:50', Rule::unique('config')],
        ])->validate();

        try {
            $config = PbConfig::create($request->all());

            $request->session()->flash('flash.banner', 'Config Created Successfully!');
            $request->session()->flash('flash.bannerStyle', 'success');

            return redirect()->route('configs.show', $config);
        } catch (Exception $e) {
            $request->session()->flash('flash.banner', 'Config couldn\'t be created!');
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->route('configs.create');
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
     * @throws ValidationException
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        Validator::make($request->all(), [
            'name' => ['required', 'max:190'],
            'configkey' => ['required', 'max:50', Rule::unique('config')->ignore($id)],
        ])->validate();

        $config = PbConfig::find($id);
        try {
            $config->update($request->all());

            $request->session()->flash('flash.banner', 'Config Created Successfully!');
            $request->session()->flash('flash.bannerStyle', 'success');
        } catch (Exception $e) {
            $request->session()->flash('flash.banner', 'Config couldn\'t be updated!');
            $request->session()->flash('flash.bannerStyle', 'danger');
        }

        return redirect()->route('configs.show', $id);
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
        } catch (Exception $e) {
            $request->session()->flash('flash.banner', 'Config couldn\'t be deleted!');
            $request->session()->flash('flash.bannerStyle', 'danger');
        }

        return redirect()->route('configs.index');
    }
}
