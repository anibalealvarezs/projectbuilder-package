<?php

namespace Anibalealvarezs\Projectbuilder\Helpers;

use Anibalealvarezs\Projectbuilder\Models\PbUser;

use Auth;

trait ControllerTrait {

    protected function globalInertiaShare()
    {
        return Shares::list([
            'navigations',
            'locale',
        ]);
    }

    protected function getAllowed($permissions)
    {
        $allowed = [];
        if (is_array($permissions)) {
            foreach($permissions as $permission) {
                $allowed[$permission] = PbUser::find(Auth::user()->id)->hasPermissionTo($permission);
            }
        } else {
            $allowed[$permissions] = PbUser::find(Auth::user()->id)->hasPermissionTo($permissions);
        }

        return $allowed;
    }
}
