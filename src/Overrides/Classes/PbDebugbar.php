<?php

namespace Anibalealvarezs\Projectbuilder\Overrides\Classes;

use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Barryvdh\Debugbar\Facades\Debugbar;
use Closure;
use Illuminate\Support\Facades\Auth;

class PbDebugbar
{
    /**
     * Transform the resource into an array.
     *
     * @return void
     */
    public static function toggleStatus(): void
    {
        if(!self::isDebugEnabled()) {
            Debugbar::disable();
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

    /**
     * Transform the resource into an array.
     *
     * @param mixed $message
     * @param string $label
     * @param bool|null $cacheOnly
     * @return void
     */
    public static function add(mixed $message, string $label = 'info', bool $cacheOnly = null): void
    {
        if (self::isDebugEnabled() && (!$cacheOnly || getConfigValue('_CACHE_ENABLED_'))) {
            Debugbar::addMessage($message, $label);
        }
    }

    /**
     * Starts a measure
     *
     * @param string $name Internal name, used to stop the measure
     * @param string|null $label Public name
     */
    public function start(string $name, string $label = null)
    {
        if (self::isDebugEnabled()) {
            Debugbar::startMeasure($name, $label);
        }
    }

    /**
     * Stops a measure
     *
     * @param string $name
     */
    public function stop(string $name)
    {
        if (self::isDebugEnabled()) {
            Debugbar::stopMeasure($name);
        }
    }

    /**
     * Stops a measure
     *
     * @param string $label
     * @param Closure $closure
     */
    public function measure(string $label, Closure $closure)
    {
        if (self::isDebugEnabled()) {
            Debugbar::measure($label, $closure);
        } else {
            $closure();
        }
    }
}
