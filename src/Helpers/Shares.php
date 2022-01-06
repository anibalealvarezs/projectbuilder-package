<?php

namespace Anibalealvarezs\Projectbuilder\Helpers;

use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Models\PbNavigation;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;
use Anibalealvarezs\Projectbuilder\Models\PbRole;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use JetBrains\PhpStorm\ArrayShape;

class Shares
{
    /**
     * Transform the resource into an array.
     *
     * @param $elements
     * @return array
     */
    public static function list($elements): array
    {
        $list = [];
        foreach ($elements as $element) {
            switch ($element) {
                case "user_permissions_and_roles":
                    $list = [
                        ...$list,
                        ...self::getUserPermissionsAndRoles()
                    ];
                    break;
                case "navigations":
                    $list = [
                        ...$list,
                        ...self::getNavigations()
                    ];
                    break;
                case "locale":
                    $list = [
                        ...$list,
                        ...self::getCustomLocale()
                    ];
                    break;
                case "permissions":
                    $list = [
                        ...$list,
                        ...self::getPermissions()
                    ];
                    break;
                case "permissionsall":
                    $list = [
                        ...$list,
                        ...self::getPermissionsAll()
                    ];
                    break;
                case "roles":
                    $list = [
                        ...$list,
                        ...self::getRoles()
                    ];
                    break;
                case "languages":
                    $list = [
                        ...$list,
                        ...self::getLanguages()
                    ];
                    break;
                case "countries":
                    $list = [
                        ...$list,
                        ...self::getCountries()
                    ];
                    break;
                case "me":
                    $list = [
                        ...$list,
                        ...self::getMyData()
                    ];
                    break;
                case "api_data":
                    $list = [
                        ...$list,
                        ...self::apiData()
                    ];
                    break;
                case "debug_status":
                    $list = [
                        ...$list,
                        ...['debug_enabled' => PbDebugbar::isDebugEnabled()]
                    ];
                    break;
                default:
                    break;
            }
        }
        return $list;
    }

    /**
     * Transform the resource into an array.
     *
     * @param $elements
     * @return array
     */
    #[ArrayShape(['allowed' => "array"])] public static function allowed($elements): array
    {
        $allowed = [];
        foreach($elements as $key => $value) {
            $allowed[$value] = PbUser::current()->hasPermissionTo($key);
        }
        return [
            'allowed' => $allowed
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['userdata' => "array|null"])] public static function getUserPermissionsAndRoles(): array
    {
        $user = PbUser::current();
        return [
            'userdata' => $user ? [
                'permissions' => $user->getAllPermissions(),
                'roles' => $user->getRoleNames(),
            ] : null,
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['navigations' => "array"])] public static function getNavigations(): array
    {
        $user = PbUser::current();
        $userPermissions = $user->getAllPermissions()->pluck('id');
        $navigations = PbNavigation::with(['ascendant', 'descendants'])
            ->where('parent', 0)
            ->where(function ($query) use ($userPermissions) {
                $query->whereIn('permission_id', $userPermissions)->get();
                $query->orWhere('permission_id', 0);
                $query->orWhereNull('permission_id');
            })
            ->orderBy('position')
            ->get();

        return [
            'navigations' => [
                'firstlevel' => $navigations,
                'full' => PbNavigation::orderBy('parent')->orderBy('position')->get()
            ]
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['locale' => "string"])] public static function getCustomLocale(): array
    {
        $customLocale = PbHelpers::getCustomLocale();
        return [
            'locale' => $customLocale ?: app()->getLocale()
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['permissions' => "mixed"])] public static function getPermissions(): array
    {
        $permissions = PbPermission::whereIn('id', PbUser::current()->getAllPermissions()->pluck('id'))->get();
        return [
            'permissions' => $permissions
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['permissionsall' => "mixed"])] public static function getPermissionsAll(): array
    {
        // Temporarily all permissions will be limited to the user's
        return [
            'permissionsall' => self::getPermissions()['permissions']
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['roles' => "mixed"])] public static function getRoles(): array
    {
        $user = PbUser::current();
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

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['languages' => "mixed"])] public static function getLanguages(): array
    {
        return [
            'languages' => PbLanguage::enabled()->get()
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['countries' => "\Anibalealvarezs\Projectbuilder\Models\PbCountry[]|\Illuminate\Database\Eloquent\Collection"])] public static function getCountries(): array
    {
        return [
            'countries' => PbCountry::all()
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['me' => "mixed"])] public static function getMyData(): array
    {
        return [
            'me' => PbUser::withPublicRelations()->current()
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['api_data' => "array"])] public static function apiData(): array
    {
        return [
            'api_data' => [
                'access' => PbUser::current()->hasPermissionTo('api access'),
                'enabled' => (bool) PbConfig::getValueByKey('_API_ENABLED_'),
            ]
        ];
    }
}
