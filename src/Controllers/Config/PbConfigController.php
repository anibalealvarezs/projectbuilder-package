<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Config;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use App\Http\Requests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\Rule;

use Auth;
use DB;
use Inertia\Response as InertiaResponse;
use Session;

class PbConfigController extends PbBuilderController
{
    function __construct(Request $request, $crud_perms = false)
    {
        $this->varsObject([
            'keys' => [
                'level' => 'Config'
            ],
            'validationRules' => [
                'name' => ['required', 'max:190'],
                'configvalue' => ['required'],
                'description' => []
            ],
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
     */
    public function index(
        int $page = 1,
        int $perpage = 0,
        string $orderby = null,
        string $field = 'id',
        string $order = 'asc',
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): InertiaResponse|JsonResponse|RedirectResponse {

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

    /**
     * Show the form for creating a new resource.
     *
     * @param string $route
     * @return InertiaResponse|JsonResponse
     */
    public function create(string $route = 'level'): InertiaResponse|JsonResponse
    {
        $this->pushRequired(['configkey']);

        return parent::create();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse|null
     */
    public function store(Request $request): Redirector|RedirectResponse|Application|null
    {
        $this->vars->validationRules['configkey'] = ['required', 'max:50', Rule::unique($this->vars->level->table)];

        return parent::store($request);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse
     */
    public function edit(
        int $id,
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): InertiaResponse|JsonResponse {

        $this->pushRequired(['configkey']);

        return parent::edit($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Application|Redirector|RedirectResponse|null
     */
    public function update(Request $request, int $id): Redirector|RedirectResponse|Application|null
    {
        $this->vars->validationRules['configkey'] = ['required', 'max:50', Rule::unique($this->vars->level->table)->ignore($id)];

        return parent::update($request, $id);
    }
}
