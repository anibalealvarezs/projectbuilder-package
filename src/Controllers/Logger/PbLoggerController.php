<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Logger;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
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
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse|JsonResponse|RedirectResponse
     */
    public function index($element = null, bool $multiple = false, string $route = 'level'): InertiaResponse|JsonResponse|RedirectResponse
    {
        $model = $this->vars->level->modelPath::withPublicRelations()->get();

        return parent::index($model);
    }
}
