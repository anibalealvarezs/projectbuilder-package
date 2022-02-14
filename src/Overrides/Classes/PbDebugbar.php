<?php

namespace Anibalealvarezs\Projectbuilder\Overrides\Classes;

use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Auth;

class PbDebugbar extends Debugbar
{
    /**
     * Transform the resource into an array.
     *
     * @return void
     */
    public static function toggleStatus(): void
    {
        if(!self::isDebugEnabled()) {
            self::disable();
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @param mixed $message
     * @param string $label
     * @return void
     */
    public static function addMessage(mixed $message, string $label = 'info'): void
    {
        if (self::isDebugEnabled()) {
            Debugbar::addMessage($message, $label);
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @return bool
     */
    public static function isDebugEnabled(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        if (!app(PbCurrentUser::class)->hasPermissionTo('developer options')) {
            return false;
        }

        return self::getDebugStatus();
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return bool
     */
    public static function getDebugStatus(): bool
    {
        return (bool) getConfigValue('_DEBUG_MODE_');
    }
}
