<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Navigation;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
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

    use PbControllerTrait;

    function __construct()
    {
        // Middlewares
        $this->middleware(['role_or_permission:crud super-admin']);
        // Variables
        $this->aeas = new AeasHelpers();
        $this->name = "navigations";
        $this->table = (new PbNavigation())->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $navigations = PbNavigation::with('permission')->get();

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'permissionsall',
                ]),
            )
        );

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
        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'permissionsall',
                ]),
            )
        );

        return Inertia::render($this->aeas->package . '/Navigations/CreateNavigation');
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
            'destiny' => ['required', 'max:254'],
            'type' => ['required', 'max:254', Rule::in(['route', 'path', 'custom'])],
            'parent' => ['required', 'integer'],
            'permission' => ['required', 'integer'],
        ]);
        $this->validationCheck($validator, $request);

        // Requests
        $name = $request['name'];
        $destiny = $request['destiny'];
        $type = $request['type'];
        $parent = $request['parent'];
        $permission = $request['permission'];

        // Process
        try {
            $navigation = new PbNavigation();
            $navigation->name = $name;
            $navigation->destiny = $destiny;
            $navigation->type = $type;
            $navigation->parent = $parent;
            $navigation->permission_id = $permission;
            $navigation->save();

            return $this->redirectResponseCRUDSuccess($request, 'Navigation created successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'Navigation could not be created!');
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
        $navigation = PbNavigation::find($id);

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
            )
        );

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
        $navigation = PbNavigation::find($id);

        Inertia::share(
            'shared',
            array_merge(
                $this->globalInertiaShare(),
                Shares::list([
                    'permissionsall',
                ]),
            )
        );

        return Inertia::render($this->aeas->package . '/Navigations/EditNavigation', [
            'pbnavigation' => $navigation,
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
            'destiny' => ['required', 'max:254'],
            'type' => ['required', 'max:254', Rule::in(['route', 'path', 'custom'])],
            'parent' => ['required', 'integer'],
            'permission' => ['required', 'integer'],
        ]);
        $this->validationCheck($validator, $request);

        // Requests
        $name = $request['name'];
        $destiny = $request['destiny'];
        $type = $request['type'];
        $parent = $request['parent'];
        $permission = $request['permission'];

        // Process
        try {
            $navigation = PbNavigation::find($id);
            $navigation->name = $name;
            $navigation->destiny = $destiny;
            $navigation->type = $type;
            $navigation->parent = $parent;
            $navigation->permission_id = $permission;
            $navigation->save();

            return $this->redirectResponseCRUDSuccess($request, 'Navigation updated successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'Navigation could not be updated!');
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
            $navigation = PbNavigation::find($id);
            $navigation->delete();

            return $this->redirectResponseCRUDSuccess($request, 'Navigation deleted successfully!');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'Navigation could not be deleted!');
        }
    }
}
