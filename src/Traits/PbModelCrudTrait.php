<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

trait PbModelCrudTrait {

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
    public function isEditableBy($id): bool
    {
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
        return true;
    }
}
