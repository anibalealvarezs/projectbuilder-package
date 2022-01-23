<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Navigation;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Inertia\Response as InertiaResponse;
use Session;

class PbNavigationController extends PbBuilderController
{
    function __construct(Request $request, $crud_perms = false)
    {
        $this->varsObject([
            'keys' => [
                'level' => 'Navigation'
            ],
            'validationRules' => [
                'name' => ['required', 'max:190'],
                'destiny' => ['required', 'max:254'],
                'type' => ['required', 'max:254', Rule::in(['route', 'path', 'custom'])],
                'parent' => ['required', 'integer'],
                'permission' => ['required', 'integer'],
                'module' => [],
                'status' => [],
            ],
            'replacers' => [
                'permission' => 'permission_id'
            ],
            'shares' => [
                'permissionsall',
            ],
            'sortingRef' => 'parent',
            'showId' => false,
        ]);
        // Parent construct
        parent::__construct($request, true);
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse|RedirectResponse
     */
    public function index($element = null, bool $multiple = false, string $route = 'level'): InertiaResponse|JsonResponse|RedirectResponse
    {
        $model = $this->vars->level->modelPath::withPublicRelations()->orderedByDefault()->get();

        return parent::index($model);
    }
}
