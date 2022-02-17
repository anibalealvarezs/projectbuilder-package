<?php

namespace Anibalealvarezs\Projectbuilder\Facades;

use Illuminate\Support\Facades\Facade;

class PbDebugbarFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PbDebugbar';
    }
}
