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
        // Get Permissions
        $loginPermission = PbPermission::where('name', 'login')->first();
        $readUsersPermission = PbPermission::where('name', 'read users')->first();
        $AdminRolesPermission = PbPermission::where('name', 'admin roles permissions')->first();
        $crudSuperAdminPermission = PbPermission::where('name', 'crud super-admin')->first();

        // Parents
        Navigation::create(['name' => 'Dashboard', 'destiny' => 'dashboard', 'type' => 'route', 'parent' => 0, 'permission_id' => $loginPermission->id, 'module' => null]);
        $usersParent = Navigation::create(['name' => 'Users & Roles', 'destiny' => '#navigation-users-roles', 'type' => 'custom', 'parent' => 0, 'permission_id' => $readUsersPermission->id, 'module' => null]);
        $settingsParent = Navigation::create(['name' => 'Settings', 'destiny' => '#navigation-settings', 'type' => 'custom', 'parent' => 0, 'permission_id' => $crudSuperAdminPermission->id, 'module' => null]);

        // Children
        if ($usersParent) {
            Navigation::create(['name' => 'Users', 'destiny' => 'users.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readUsersPermission->id, 'module' => 'pbuser']);
            Navigation::create(['name' => 'Roles', 'destiny' => 'roles.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $AdminRolesPermission->id, 'module' => 'pbroles']);
            Navigation::create(['name' => 'Permissions', 'destiny' => 'permissions.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $AdminRolesPermission->id, 'module' => 'pbpermissions']);
        }
        if ($settingsParent) {
            Navigation::create(['name' => 'Config', 'destiny' => 'configs.index', 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $crudSuperAdminPermission->id, 'module' => 'pbconfig']);
            Navigation::create(['name' => 'Navigations', 'destiny' => 'navigations.index', 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $crudSuperAdminPermission->id, 'module' => 'pbnavigations']);
        }
    }
}
