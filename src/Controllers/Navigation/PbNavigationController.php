<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Navigation;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use App\Http\Requests;

use Illuminate\Validation\Rule;

use Auth;
use DB;
use Session;

use Inertia\Response as InertiaResponse;

class PbNavigationController extends PbBuilderController
{
    function __construct($crud_perms = false)
    {
        // Vars Override
        $this->key = 'Navigation';
        // Parent construct
        parent::__construct(true);
        // Validation Rules
        $this->validationRules = [
            'name' => ['required', 'max:190'],
            'destiny' => ['required', 'max:254'],
            'type' => ['required', 'max:254', Rule::in(['route', 'path', 'custom'])],
            'parent' => ['required', 'integer'],
            'permission' => ['required', 'integer'],
            'module' => [],
        ];
        // Model fields name replacing
        $this->replacers = [
            'permission' => 'permission_id'
        ];
        // Additional variables to share
        $this->shares = [
            'permissionsall',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return InertiaResponse
     */
    public function index($element = null, bool $multiple = false, string $route = 'level'): InertiaResponse
    {
        $model = $this->modelPath::with('permission')->get();

        return parent::index($model);
    }
}
