<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait PbModelTrait {

    /**
     * @var array
     */
    protected array $publicRelations = [];

    /**
     * @var array
     */
    protected array $allRelations = [];

    /**
     * @var array
     */
    public array $undeletableModels = [];

    /**
     * @var array
     */
    public array $unmodifiableModels = [];

    /**
     * @var array
     */
    public array $unreadableModels = [];

    /**
     * @var array
     */
    public array $unconfigurableModels = [];

    /**
     * Scope a query to only include popular users.
     *
     * @return array
     */
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

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function getReadableStatus(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        foreach($this->unreadableModels as $key => $value) {
            if (in_array($this->{$key}, $value)) {
                return false;
            }
        }

        return $this->isViewableBy(Auth::user()->id);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function getEditableStatus(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        foreach($this->unmodifiableModels as $key => $value) {
            if (in_array($this->{$key}, $value)) {
                return false;
            }
        }

        return $this->isEditableBy(Auth::user()->id);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function getSelectableStatus(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        foreach($this->unreadableModels as $key => $value) {
            if (in_array($this->{$key}, $value)) {
                return false;
            }
        }

        return $this->isSelectableBy(Auth::user()->id);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function getDeletableStatus(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        foreach($this->undeletableModels as $key => $value) {
            if (in_array($this->{$key}, $value)) {
                return false;
            }
        }

        return $this->isDeletableBy(Auth::user()->id);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function getConfigurableStatus(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        foreach($this->unconfigurableModels as $key => $value) {
            if (in_array($this->{$key}, $value)) {
                return false;
            }
        }

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

    /**
     * Scope a query to only include popular users.
     *
     * @return Array
     */
    public function getAppendedFields(): Array
    {
        return $this->appends;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public static function scopeIsEnableable(): bool
    {
        return self::$sortable ?? false;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public static function scopeIsSortable(): bool
    {
        return self::$enableable ?? false;
    }

    /**
     * Determine if the given relationship (method) exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasRelation($key)
    {
        // If the key already exists in the relationships array, it just means the
        // relationship has already been loaded, so we'll just return it out of
        // here because there is no need to query within the relations twice.
        if ($this->relationLoaded($key)) {
            return true;
        }

        // If the "attribute" exists as a method on the model, we will just assume
        // it is a relationship and will load and return results from the query
        // and hydrate the relationship's value on the "relationships" array.
        if (method_exists($this, $key)) {
            return true;
        }

        return false;
    }
}
