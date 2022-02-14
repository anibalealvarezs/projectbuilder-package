<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Anibalealvarezs\Projectbuilder\Models\PbUser;

trait PbModelCrudTrait {

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
    public function isEditableBy($id): bool
    {
        if (!$user = $this->getAuthorizedUser($id)) {
            return false;
        }

        if (!$user->hasPermissionTo('update '.$this->getTable())) {
            return false;
        }

        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
    public function isViewableBy($id): bool
    {
        if (!$user = $this->getAuthorizedUser($id)) {
            return false;
        }

        if (!$user->hasPermissionTo('read '.$this->getTable())) {
            return false;
        }

        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
    public function isSelectableBy($id): bool
    {
        return $this->isViewableBy($id);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
    public function isDeletableBy($id): bool
    {
        if (!$user = $this->getAuthorizedUser($id)) {
            return false;
        }

        if (!$user->hasPermissionTo('delete '.$this->getTable())) {
            return false;
        }

        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool|PbUser|PbCurrentUser
     */
    public function getAuthorizedUser($id): bool|PbUser|PbCurrentUser
    {
        if (!class_exists(Auth::class)) {
            return false;
        }

        if (!Auth::check()) {
            return false;
        }

        return (Auth::user()->id === $id ? app(PbCurrentUser::class) : PbUser::find($id));
    }
}
