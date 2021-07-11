<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbModule;
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
        if (Navigation::count() == 0) {
            // Get Permissions
            $loginPermission = PbPermission::where('name', 'login')->first();
            $readUsersPermission = PbPermission::where('name', 'read users')->first();
            $readRolesPermission = PbPermission::where('name', 'read roles')->first();
            $readPermissionsPermission = PbPermission::where('name', 'read permissions')->first();
            $readConfigsPermission = PbPermission::where('name', 'read configs')->first();
            $readNavigationsPermission = PbPermission::where('name', 'read navigations')->first();
            $moduleUser = PbModule::where('modulekey', 'user')->first();
            $moduleConfig = PbModule::where('modulekey', 'config')->first();
            $moduleNavigation = PbModule::where('modulekey', 'navigation')->first();
            $moduleRole = PbModule::where('modulekey', 'role')->first();
            $modulePermission = PbModule::where('modulekey', 'permission')->first();

            // Parents
            Navigation::updateOrCreate(['name' => 'Dashboard'], ['destiny' => 'dashboard', 'type' => 'route', 'parent' => 0, 'permission_id' => $loginPermission->id, 'position' => 0, 'module_id' => null]);
            $usersParent = Navigation::updateOrCreate(['name' => 'Users & Roles'], ['destiny' => '#navigation-users-roles', 'type' => 'custom', 'parent' => 0, 'permission_id' => $readUsersPermission->id, 'position' => 1, 'module_id' => $moduleUser->id]);
            $settingsParent = Navigation::updateOrCreate(['name' => 'Settings'], ['destiny' => '#navigation-settings', 'type' => 'custom', 'parent' => 0, 'permission_id' => $readConfigsPermission->id, 'position' => 2, 'module_id' => null]);

            // Children
            if ($usersParent) {
                Navigation::updateOrCreate(['name' => 'Users'], ['destiny' => 'users.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readUsersPermission->id, 'position' => 0, 'module_id' => $moduleUser->id]);
                Navigation::updateOrCreate(['name' => 'Roles'], ['destiny' => 'roles.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readRolesPermission->id, 'position' => 1, 'module_id' => $moduleRole->id]);
                Navigation::updateOrCreate(['name' => 'Permissions'], ['destiny' => 'permissions.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readPermissionsPermission->id, 'position' => 2, 'module_id' => $modulePermission->id]);
            }
            if ($settingsParent) {
                Navigation::updateOrCreate(['name' => 'Config'], ['destiny' => 'configs.index', 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $readConfigsPermission->id, 'position' => 0, 'module_id' => $moduleConfig->id]);
                Navigation::updateOrCreate(['name' => 'Navigations'], ['destiny' => 'navigations.index', 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $readNavigationsPermission->id, 'position' => 1, 'module_id' => $moduleNavigation->id]);
            }
        }
    }
}
