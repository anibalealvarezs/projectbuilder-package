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
     * @param string|null $orderby
     * @param string $field
     * @param string $order
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse|RedirectResponse
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
        $config = $this->vars->level->modelPath::getCrudConfig();

        $query = $this->vars->level->modelPath::withPublicRelations();

        if (!isset($this->vars->level->modelPath::$sortable) || !$this->vars->level->modelPath::$sortable) {
            if (!$perpage && isset($config['pagination']['per_page']) && $config['pagination']['per_page']) {
                $perpage = $config['pagination']['per_page'];
            }
            if ($orderby) {
                $query->orderBy($field, $order);
            }
            $model = $query->paginate($perpage ?? PbConfig::getValueByKey('_DEFAULT_TABLE_SIZE_') ?: 10, ['*'], 'page', $page ?? 1);
        } else {
            $model = $query->get();
        }

        return parent::index($page, $perpage, $orderby, $field, $order, $model);
    }
}
