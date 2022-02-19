<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Config;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

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
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;
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
     * Show the form for creating a new resource.
     *
     * @param string $route
     * @return InertiaResponse|JsonResponse
     * @throws ReflectionException|InvalidArgumentException
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
     * @throws ReflectionException
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
     * @return RedirectResponse|InertiaResponse|JsonResponse
     * @throws ReflectionException|InvalidArgumentException
     */
    public function edit(
        int $id,
        $element = null,
        bool $multiple = false,
        string $route = 'level'
    ): RedirectResponse|InertiaResponse|JsonResponse {

        $this->pushRequired(['configkey']);

        return parent::edit($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Application|Redirector|RedirectResponse|null
     * @throws ReflectionException
     */
    public function update(Request $request, int $id): Redirector|RedirectResponse|Application|null
    {
        $this->vars->validationRules['configkey'] = ['required', 'max:50', Rule::unique($this->vars->level->table)->ignore($id)];

        return parent::update($request, $id);
    }
}
