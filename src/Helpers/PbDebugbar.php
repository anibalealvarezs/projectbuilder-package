<?php

namespace Anibalealvarezs\Projectbuilder\Helpers;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Auth;

class PbDebugbar extends Debugbar
{
    public static function toggleStatus()
    {
        if(!self::isDebugEnabled()) {
            self::disable();
        }
    }

    public static function addMessage(mixed $message, string $label = 'info')
    {
        if (self::isDebugEnabled()) {
            Debugbar::addMessage($message, $label);
        }
    }

    public static function isDebugEnabled()
    {
        $isLogged = Auth::check();
        $canDebug = false;
        if ($isLogged) {
            $canDebug = PbUser::current()->hasPermissionTo('developer options');
        }
        return PbHelpers::getDebugStatus() && $isLogged && $canDebug;
    }
}
