<?php

namespace Anibalealvarezs\Projectbuilder\Middleware;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PbRoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $roleOrPermission, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $user = PbUser::find(Auth::guard($guard)->user()->id);
        if ($user) {
            $rolesOrPermissions = is_array($roleOrPermission)
                ? $roleOrPermission
                : explode('|', $roleOrPermission);

            if (!$user->hasAnyRole($rolesOrPermissions) && !$user->hasAnyPermission($rolesOrPermissions)) {
                throw UnauthorizedException::forRolesOrPermissions($rolesOrPermissions);
            }

            return $next($request);
        } else {
            throw UnauthorizedException::notLoggedIn();
        }
    }
}
