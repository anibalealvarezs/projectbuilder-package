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
use ReflectionException;
use Session;

use Inertia\Response as InertiaResponse;

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
     * @param int $page
     * @param int $perpage
     * @param string|null $orderby
     * @param string $field
     * @param string $order
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse|RedirectResponse
     * @throws ReflectionException
     */
    public function index(
        int $page = 1,
        int $perpage = 0,
        string $orderby = null,
        string $field = 'id',
        string $order = 'asc',
        $element = null,
        bool $multiple = false,
        string $route = 'level'): InertiaResponse|JsonResponse|RedirectResponse
    {
        $this->startController(getClassName(__CLASS__));

        // Set cache/methods arguments
        $this->initArgs([
            'class' => 'model_controller',
            'pagination' => ['page' => $page, 'perpage' => $perpage, 'orderby' => $orderby, 'field' => $field, 'order' => $order],
            'byRoles' => true,
        ]);

        // Get models list
        $this->measuredRun(return: $model, name: getClassName(__CLASS__) . ' - models list build', args: [
            'closure' => fn() => $this->vars->level->modelPath::withPublicRelations()->orderedByDefault()->get(),
            'modelFunction' => 'getList',
        ]);

        $this->stopController(getClassName(__CLASS__));

        return parent::index(
            element: $model,
        );
    }
}
