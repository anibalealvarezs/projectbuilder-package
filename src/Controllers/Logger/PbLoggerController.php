<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Logger;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Response as InertiaResponse;

class PbLoggerController extends PbBuilderController
{
    function __construct(Request $request, $crud_perms = false)
    {
        $this->varsObject([
            'keys' => [
                'level' => 'Logger'
            ],
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

        $model = $this->vars->level->modelPath::withPublicRelations()
            ->paginate($perpage ?? PbConfig::getValueByKey('_DEFAULT_TABLE_SIZE_') ?: 10, ['*'], 'page', $page ?? 1);

        return parent::index($page, $perpage, $model);
    }
}
