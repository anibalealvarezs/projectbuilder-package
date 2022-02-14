<?php

namespace Anibalealvarezs\Projectbuilder\Utilities;

use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Anibalealvarezs\Projectbuilder\Models\PbNavigation;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;
use Anibalealvarezs\Projectbuilder\Models\PbRole;
use Anibalealvarezs\Projectbuilder\Overrides\Classes\PbDebugbar;
use JetBrains\PhpStorm\ArrayShape;

class PbShares
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
            $list = match ($element) {
                "user_permissions_and_roles" => [...$list, ...self::getUserPermissionsAndRoles()],
                "navigations" => [...$list, ...self::getNavigations()],
                "locale" => [...$list, ...self::getCustomLocale()],
                "permissions" => [...$list, ...self::getPermissions()],
                "permissionsall" => [...$list, ...self::getPermissionsAll()],
                "roles" => [...$list, ...self::getRoles()],
                "languages" => [...$list, ...self::getLanguages()],
                "countries" => [...$list, ...self::getCountries()],
                "me" => [...$list, ...self::getMyData()],
                "api_data" => [...$list, ...self::apiData()],
                "debug_status" => [...$list, ...['debug_enabled' => PbDebugbar::isDebugEnabled()]],
                "modules" => [...$list, ...self::getModules()],
                "modules_replace" => [...$list, ...self::getModulesReplacingIds()],
            };
        }
        return $list;
    }

    /**
     * Transform the resource into an array.
     *
     * @param $elements
     * @return array
     */
    #[ArrayShape(['allowed' => "array"])]
    public static function allowed($elements): array
    {
        $allowed = [];
        foreach ($elements as $key => $value) {
            $allowed[$value] = app(PbCurrentUser::class)->hasPermissionTo($key);
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
    #[ArrayShape(['userdata' => "array|null"])]
    public static function getUserPermissionsAndRoles(): array
    {
        $user = app(PbCurrentUser::class);
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
    #[ArrayShape(['navigations' => "array"])]
    public static function getNavigations(): array
    {
        $userPermissions = app(PbCurrentUser::class)->permissions->pluck('id');
        $navigations = PbNavigation::with(['ascendant', 'descendants' => function ($q) use ($userPermissions) {
                $q->enabled();
                $q->where(function ($query) use ($userPermissions) {
                    $query->whereIn('permission_id', $userPermissions);
                    $query->orWhere('permission_id', 0);
                    $query->orWhereNull('permission_id');
                });
            }])
            ->enabled()
            ->where(function ($query) use ($userPermissions) {
                $query->whereIn('permission_id', $userPermissions);
                $query->orWhere('permission_id', 0);
                $query->orWhereNull('permission_id');
            });

        return [
            'navigations' => [
                'firstlevel' => $navigations->where('parent', 0)->orderBy('position')->get(),
                'full' => $navigations->orderBy('parent')->orderBy('position')->get(),
            ]
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['locale' => "string"])]
    public static function getCustomLocale(): array
    {
        $customLocale = getCurrentLocale();
        return [
            'locale' => $customLocale ?: app()->getLocale()
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['permissions' => "mixed"])]
    public static function getPermissions(): array
    {
        $permissions = PbPermission::whereIn('id', app(PbCurrentUser::class)->permissions->pluck('id'))->get();
        return [
            'permissions' => $permissions
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['permissionsall' => "mixed"])]
    public static function getPermissionsAll(): array
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
    #[ArrayShape(['roles' => "mixed"])]
    public static function getRoles(): array
    {
        $user = app(PbCurrentUser::class);
        $roles = collect([]);
        if ($user->hasRole(['super-admin'])) {
            $roles = PbRole::whereNotIn(
                'name',
                [
                    'super-admin'
                ]
            )->get();
        } elseif ($user->hasRole(['admin'])) {
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
    #[ArrayShape(['languages' => "mixed"])]
    public static function getLanguages(): array
    {
        $languages = PbLanguage::enabled()->get();
        $languages->each(function ($trainee) {
            $trainee->putCountry();
        });
        return [
            'languages' => $languages
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape([
        'countries' => "\Anibalealvarezs\Projectbuilder\Models\PbCountry[]|\Illuminate\Database\Eloquent\Collection"
    ])]
    public static function getCountries(): array
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
    #[ArrayShape([
        'modules' => "\Anibalealvarezs\Projectbuilder\Models\PbModule[]|\Illuminate\Database\Eloquent\Collection"
    ])]
    public static function getModules(): array
    {
        return [
            'modules' => PbModule::all()
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape([
        'modules' => "\Anibalealvarezs\Projectbuilder\Models\PbModule[]|\Illuminate\Database\Eloquent\Collection"
    ])]
    public static function getModulesReplacingIds(): array
    {
        $modules = PbModule::all()->toArray();
        foreach($modules as &$module) {
            $module['id'] = $module['modulekey'];
        }
        return [
            'modules' => collect($modules)
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['me' => "mixed"])]
    public static function getMyData(): array
    {
        return [
            'me' => app(PbCurrentUser::class)
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    #[ArrayShape(['api_data' => "array"])]
    public static function apiData(): array
    {
        return [
            'api_data' => [
                'access' => app(PbCurrentUser::class)->hasPermissionTo('api access'),
                'enabled' => (bool) getConfigValue('_API_ENABLED_'),
            ]
        ];
    }
}
