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

        $filtered = $model->map(function ($q) {
            return $q->only([
                'id',
                'severity',
                'code',
                'message',
                'object_type',
                'object_id',
                'user_id',
                'module_id',
                'created_at',
                'crud',
            ]);
        });

        $filtered = $this->vars->helper->class::setCollectionAttributeDatetimeFormat(
            $filtered,
            ['created_at'],
            "custom",
            "d/m/y"
        );

        return parent::index($filtered);
    }
}
