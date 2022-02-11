<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;

trait PbModelCrudTrait {

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
    public function isEditableBy($id): bool
    {
        if ($permissions = app(PbCurrentUser::class)->currentPermissions(true)) {
            if (!in_array('update '.$this->getTable(), $permissions)) {
                return false;
            }
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
        if ($permissions = app(PbCurrentUser::class)->currentPermissions(true)) {
            if (!in_array('read '.$this->getTable(), $permissions)) {
                return false;
            }
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
        if ($permissions = app(PbCurrentUser::class)->currentPermissions(true)) {
            if (!in_array('read '.$this->getTable(), $permissions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
    public function isDeletableBy($id): bool
    {
        if ($permissions = app(PbCurrentUser::class)->currentPermissions(true)) {
            if (!in_array('delete '.$this->getTable(), $permissions)) {
                return false;
            }
        }

        return true;
    }
}
