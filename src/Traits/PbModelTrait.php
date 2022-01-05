<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Illuminate\Database\Eloquent\Builder;

trait PbModelTrait {

    /**
     * @var array
     */
    protected array $publicRelations = [];

    /**
     * @var array
     */
    protected array $allRelations = [];

    protected function getCrudAttribute(): array
    {
        return [
            'readable' => $this->getReadableStatus(),
            'editable' => $this->getEditableStatus(),
            'selectable' => $this->getSelectableStatus(),
            'deletable' => $this->getDeletableStatus(),
            'configurable' => $this->getConfigurableStatus(),
        ];
    }

    protected function getReadableStatus(): bool
    {
        return true;
    }

    protected function getEditableStatus(): bool
    {
        return true;
    }

    protected function getSelectableStatus(): bool
    {
        return true;
    }

    protected function getDeletableStatus(): bool
    {
        return true;
    }

    protected function getConfigurableStatus(): bool
    {
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithPublicRelations(Builder $query): Builder
    {
        if (isset($this->publicRelations) && !empty($this->publicRelations)) {
            foreach ($this->publicRelations as $relation) {
                $query->with($relation);
            }
        }
        return $query;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithAllRelations(Builder $query): Builder
    {
        if (isset($this->allRelations) && !empty($this->allRelations)) {
            foreach ($this->allRelations as $relation) {
                $query->with($relation);
            }
        }
        return $query;
    }
}
