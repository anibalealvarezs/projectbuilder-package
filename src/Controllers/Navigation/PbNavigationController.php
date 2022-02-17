<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Navigation;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use Anibalealvarezs\Projectbuilder\Facades\PbDebugbarFacade as Debug;
use Anibalealvarezs\Projectbuilder\Utilities\PbCache;
use App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Inertia\Response as InertiaResponse;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;
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
     * @param int $page
     * @param int $perpage
     * @param string|null $orderby
     * @param string $field
     * @param string $order
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse|RedirectResponse
     * @throws ReflectionException|InvalidArgumentException
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
        Debug::start('custom_controller', $this->vars->level->names.' crud controller');

        Debug::measure(
            $this->vars->level->names.' crud controller - model list build',
            function() use (&$model, $page, $perpage, $orderby, $field, $order) {
                $cached = PbCache::run(
                    closure: fn() =>  $this->vars->level->modelPath::withPublicRelations()->orderedByDefault()->get(),
                    package: $this->vars->helper->package,
                    class: __CLASS__,
                    model: $this->vars->level->names,
                    modelFunction: 'getList',
                    byRoles: true,
                );
                $model = $cached['data'];
                $this->vars->cacheObjects[] = $cached['index'];
            }
        );

        Debug::stop('custom_controller');

        return parent::index(
            $page,
            $perpage,
            $orderby,
            $field,
            $order,
            $model
        );
    }
}
