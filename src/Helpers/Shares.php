<?php

namespace Anibalealvarezs\Projectbuilder\Helpers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Models\PbNavigation;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;
use Anibalealvarezs\Projectbuilder\Models\PbRole;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Illuminate\Support\Facades\Auth;

class Shares
{
    public static function list($elements): array
    {
        $list = [];
        foreach ($elements as $element) {
            switch ($element) {
                case "user_permissions_and_roles":
                    $list = array_merge($list, self::getUserPermissionsAndRoles());
                    break;
                case "navigations":
                    $list = array_merge($list, self::getNavigations());
                    break;
                case "locale":
                    $list = array_merge($list, self::getCustomLocale());
                    break;
                case "permissions":
                    $list = array_merge($list, self::getPermissions());
                    break;
                case "permissionsall":
                    $list = array_merge($list, self::getPermissionsAll());
                    break;
                case "roles":
                    $list = array_merge($list, self::getRoles());
                    break;
                case "languages":
                    $list = array_merge($list, self::getLanguages());
                    break;
                case "countries":
                    $list = array_merge($list, self::getCountries());
                    break;
                case "me":
                    $list = array_merge($list, self::getMyData());
                    break;
                default:
                    break;
            }
        }
        return $list;
    }

    public static function allowed($elements): array
    {
        $allowed = [];
        foreach($elements as $key => $value) {
            $allowed[$value] = PbUser::find(Auth::user()->id)->hasPermissionTo($key);
        }
        return [
            'allowed' => $allowed
        ];
    }

    public static function getUserPermissionsAndRoles()
    {
        $user = PbUser::find(Auth::user()->id);
        return [
            'userdata' => Auth::user() ? [
                'permissions' => $user->getAllPermissions(),
                'roles' => $user->getRoleNames(),
            ] : null,
        ];
    }

    public static function getNavigations(): array
    {
        $user = PbUser::find(Auth::user()->id);
        $userPermissions = $user->getAllPermissions()->pluck('id');
        $navigations = PbNavigation::with(['ascendant', 'descendants'])
                            ->where('parent', 0)
                            ->where(function ($query) use ($userPermissions) {
                                $query->whereIn('permission_id', $userPermissions)->get();
                                $query->orWhere('permission_id', 0);
                                $query->orWhereNull('permission_id');
                            })->get();

        return [
            'navigations' => [
                'firstlevel' => $navigations,
                'full' => PbNavigation::all()
            ]
        ];
    }

    public static function getCustomLocale(): array
    {
        $customLocale = PbHelpers::getCustomLocale();
        return [
            'locale' => $customLocale ?: app()->getLocale()
        ];
    }

    public static function getPermissions(): array
    {
        $user = PbUser::find(Auth::user()->id);
        $permissions = ([]);
        if ($user->hasRole(['super-admin'])) {
            $permissions = PbPermission::whereNotIn(
                'name',
                [
                    'crud super-admin',
                    'admin roles permissions',
                    'manage app',
                    'create roles',
                    'update roles',
                    'delete roles',
                    'create configs',
                    'update configs',
                    'delete configs',
                    'create permissions',
                    'update permissions',
                    'delete permissions',
                    'create navigations',
                    'update navigations',
                    'delete navigations',
                ]
            )->get();
        } elseif($user->hasRole(['admin'])) {
            $permissions = PbPermission::whereNotIn(
                'name',
                [
                    'crud super-admin',
                    'admin roles permissions',
                    'manage app',
                    'create users',
                    'update users',
                    'delete users',
                    'create roles',
                    'update roles',
                    'delete roles',
                    'create configs',
                    'update configs',
                    'delete configs',
                    'create permissions',
                    'update permissions',
                    'delete permissions',
                    'create navigations',
                    'update navigations',
                    'delete navigations',
                ]
            )->get();
        }
        return [
            'permissions' => $permissions
        ];
    }

    public static function getPermissionsAll(): array
    {
        return [
            'permissionsall' => PbPermission::all()
        ];
    }

    public static function getRoles(): array
    {
        $user = PbUser::find(Auth::user()->id);
        $roles = ([]);
        if ($user->hasRole(['super-admin'])) {
            $roles = PbRole::whereNotIn(
                'name',
                [
                    'super-admin'
                ]
            )->get();
        } elseif($user->hasRole(['admin'])) {
            $roles = PbRole::whereNotIn(
                'name',
                [
                    'super-admin',
                    'admin'
                ]
            )->get();
        }
        return [
            'roles' => $roles
        ];
    }

    public static function getLanguages(): array
    {
        return [
            'languages' => PbLanguage::getEnabled()->get()
        ];
    }

    public static function getCountries(): array
    {
        return [
            'countries' => PbCountry::all()
        ];
    }

    public static function getMyData(): array
    {
        return [
            'me' => PbUser::with('country', 'city', 'lang', 'roles')->find(Auth::user()->id)
        ];
    }
}
