<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Illuminate\Database\Eloquent\Builder;

trait PbModelEnableableTrait
{
    public static bool $enableable = true;

    /**
     * Boot the model.
     *
     * @return bool
     */
    public function scopeEnable(): bool
    {
        return $this->update(['status' => true]);
    }

    /**
     * Boot the model.
     *
     * @return bool
     */
    public function scopeDisable(): bool
    {
        return $this->update(['status' => false]);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDisabled(Builder $query): Builder
    {
        return $query->where('status', true);
    }
}
