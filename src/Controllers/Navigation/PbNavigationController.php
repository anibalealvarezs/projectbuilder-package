<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Navigation;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use Anibalealvarezs\Projectbuilder\Models\PbConfig;
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
     * @param int $page
     * @param int $perpage
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse|RedirectResponse
     */
    public function index(int $page = 1, int $perpage = 0, $element = null, bool $multiple = false, string $route = 'level'): InertiaResponse|JsonResponse|RedirectResponse
    {
        $config = $this->vars->level->modelPath::getCrudConfig();
        if (!$perpage && isset($config['pagination']['per_page']) && $config['pagination']['per_page']) {
            $perpage = $config['pagination']['per_page'];
        }

        $model = $this->vars->level->modelPath::withPublicRelations()->orderedByDefault()
            ->paginate($perpage ?? PbConfig::getValueByKey('_DEFAULT_TABLE_SIZE_') ?: 10, ['*'], 'page', $page ?? 1);

        return parent::index($page, $perpage, $model);
    }
}
