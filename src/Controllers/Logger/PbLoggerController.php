<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Logger;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use App\Http\Requests;
use Illuminate\Http\Request;

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
}
