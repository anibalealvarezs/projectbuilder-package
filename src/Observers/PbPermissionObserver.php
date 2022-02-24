<?php

namespace Anibalealvarezs\Projectbuilder\Observers;

use Anibalealvarezs\Projectbuilder\Models\PbNavigation;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;

class PbPermissionObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param PbPermission $permission
     * @return void
     */
    public function created(PbPermission $permission)
    {
        //
    }

    /**
     * Handle the User "updated" event.
     *
     * @param PbPermission $permission
     * @return void
     */
    public function updated(PbPermission $permission)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param PbPermission $permission
     * @return void
     */
    public function deleted(PbPermission $permission)
    {
        PbNavigation::removePermission($permission);
    }

    /**
     * Handle the User "forceDeleted" event.
     *
     * @param PbPermission $permission
     * @return void
     */
    public function forceDeleted(PbPermission $permission)
    {
        //
    }
}
