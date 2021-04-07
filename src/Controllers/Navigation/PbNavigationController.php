<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Navigation;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbNavigation;

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

class PbNavigationController extends Controller
{
    protected $aeas;
    protected $name;
    protected $table;

    function __construct()
    {
        $this->aeas = new AeasHelpers();
        $this->name = "navigations";
        $Navigation = new PbNavigation();
        $this->table = $Navigation->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $navigations =  PbNavigation::all();

        return Inertia::render($this->aeas->package . '/Navigations/Navigations', [
            'pbnavigations' => $navigations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        return Inertia::render($this->aeas->package . '/Navigations/CreateNavigation');
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
            'destiny' => ['required', 'max:254'],
            'type' => ['required', 'max:254', Rule::in(['route', 'path', 'custom'])],
            'parent' => ['required', 'integer'],
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
                 PbNavigation::create($request->all());

                $request->session()->flash('flash.banner', 'Navigation Created Successfully!');
                $request->session()->flash('flash.bannerStyle', 'success');

                return redirect()->route($this->name.'.index');
            } catch (Exception $e) {
                $request->session()->flash('flash.banner', 'Navigation could not be created!');
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
        $navigation =  PbNavigation::find($id);

        return Inertia::render($this->aeas->package . '/Navigations/ShowNavigation', [
            'pbnavigation' => $navigation,
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
        $navigation =  PbNavigation::find($id);
        return Inertia::render($this->aeas->package . '/Navigations/EditNavigation', [
            'pbnavigation' => $navigation,
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
            'destiny' => ['required', 'max:254'],
            'type' => ['required', 'max:254', Rule::in(['route', 'path', 'custom'])],
            'parent' => ['required', 'integer'],
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

            $navigation =  PbNavigation::find($id);
            try {
                $navigation->update($request->all());

                $request->session()->flash('flash.banner', 'Navigation Created Successfully!');
                $request->session()->flash('flash.bannerStyle', 'success');

                return redirect()->route($this->name.'.index');
            } catch (Exception $e) {
                $request->session()->flash('flash.banner', 'Navigation couldn\'t be updated!');
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
        $navigation =  PbNavigation::find($id);
        try {
            $navigation->delete();

            $request->session()->flash('flash.banner', 'Navigation deleted successfully!');
            $request->session()->flash('flash.bannerStyle', 'success');

            return redirect()->route($this->name.'.index');
        } catch (Exception $e) {
            $request->session()->flash('flash.banner', 'Navigation couldn\'t be deleted!');
            $request->session()->flash('flash.bannerStyle', 'danger');

            return redirect()->back()->withInput();
        }
    }
}
