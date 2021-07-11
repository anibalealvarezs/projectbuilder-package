<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Navigation;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;

use App\Http\Requests;

use Illuminate\Validation\Rule;

use Auth;
use DB;
use Session;

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
            'status' => [],
        ];
        // Model fields name replacing
        $this->replacers = [
            'permission' => 'permission_id'
        ];
        // Additional variables to share
        $this->shares = [
            'permissionsall',
        ];
        // Sortable model ?
        $this->sortable = true;
        // Sortable model
        $this->sortingRef = 'parent';
        // Show position column ?
        // $this->showPosition = true;
        // Show ID column ?
        $this->showId = false;
    }

    /**
     * Display a listing of the resource.
     *
     * @param null $element
     * @param bool $multiple
     * @param string $route
     * @return void
     */
    public function index($element = null, bool $multiple = false, string $route = 'level')
    {
        $model = $this->modelPath::with(['ascendant', 'permission', 'module'])->orderBy('parent')->orderBy('position')->get();

        return parent::index($model);
    }
}
