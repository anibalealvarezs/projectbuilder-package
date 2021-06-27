<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbNavigation as Navigation;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;
use Illuminate\Database\Seeder;

class PbNavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Parents
        Navigation::create(['name' => 'Dashboard', 'destiny' => 'dashboard', 'type' => 'route', 'parent' => 0, 'permission_id' => ['login'], 'nodule' => null]);
        $usersParent = Navigation::create(['name' => 'Users & Roles', 'destiny' => '#navigation-users-roles', 'type' => 'custom', 'parent' => 0, 'permission_id' => ['read users'], 'nodule' => null]);
        $settingsParent = Navigation::create(['name' => 'Settings', 'destiny' => '#navigation-settings', 'type' => 'custom', 'parent' => 0, 'permission_id' => ['crud super-admin'], 'nodule' => null]);

        // Children
        if ($usersParent) {
            $readUsersPermission = PbPermission::where('name', 'read users')->first();
            Navigation::create(['name' => 'Users', 'destiny' => 'users.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readUsersPermission->id, 'module' => 'pbuser']);
            $AdminRolesPermission = PbPermission::where('name', 'admin roles permissions')->first();
            Navigation::create(['name' => 'Roles', 'destiny' => 'roles.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $AdminRolesPermission->id, 'module' => 'pbroles']);
            Navigation::create(['name' => 'Permissions', 'destiny' => 'permissions.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $AdminRolesPermission->id, 'module' => 'pbpermissions']);
        }
        if ($settingsParent) {
            $crudSuperAdminPermission = PbPermission::where('name', 'crud super-admin')->first();
            Navigation::create(['name' => 'Config', 'destiny' => 'configs.index', 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $crudSuperAdminPermission->id, 'module' => 'pbconfig']);
            Navigation::create(['name' => 'Navigations', 'destiny' => 'navigations.index', 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $crudSuperAdminPermission->id, 'module' => 'pbnavigations']);
        }
    }
}
