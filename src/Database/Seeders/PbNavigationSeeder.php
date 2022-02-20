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
        // Get Permissions
        $loginPermission = PbPermission::where('name', 'login')->first();
        $readUsersPermission = PbPermission::where('name', 'read users')->first();
        $readRolesPermission = PbPermission::where('name', 'read roles')->first();
        $readPermissionsPermission = PbPermission::where('name', 'read permissions')->first();
        $readConfigsPermission = PbPermission::where('name', 'read configs')->first();
        $readNavigationsPermission = PbPermission::where('name', 'read navigations')->first();
        $readLoggersPermission = PbPermission::where('name', 'read loggers')->first();
        $moduleUser = PbModule::where('modulekey', 'user')->first();
        $moduleConfig = PbModule::where('modulekey', 'config')->first();
        $moduleNavigation = PbModule::where('modulekey', 'navigation')->first();
        $moduleRole = PbModule::where('modulekey', 'role')->first();
        $modulePermission = PbModule::where('modulekey', 'permission')->first();

        Navigation::query()->delete();

        // Dashboard
        if ($dashboard = Navigation::updateOrCreate(['destiny' => 'dashboard', 'module_id' => null], ['name' => 'Dashboard', 'type' => 'route', 'parent' => 0, 'permission_id' => $loginPermission->id, 'position' => 0])->setLocale('en')) {
            // Spanish name update
            $dashboard->setLocale('es');
            $dashboard->name = 'Escritorio';
            $dashboard->save();
        }

        // Users & Roles
        if ($usersParent = Navigation::updateOrCreate(['destiny' => '#navigation-users-roles', 'module_id' => $moduleUser->id], ['name' => 'Users & Roles', 'type' => 'custom', 'parent' => 0, 'permission_id' => $readUsersPermission->id, 'position' => 1])->setLocale('en')) {
            // Spanish name update
            $usersParent->setLocale('es');
            $usersParent->name = 'Usuarios y Roles';
            $usersParent->save();
            // Children
            Navigation::upsert([
                ['destiny' => 'users.index', 'module_id' => $moduleUser->id, 'name' => json_encode(['en' => 'Users', 'es' => 'Usuarios']), 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readUsersPermission->id, 'position' => 0],
                ['destiny' => 'roles.index', 'module_id' => $moduleRole->id, 'name' => json_encode(['en' => 'Roles', 'es' => 'Roles']), 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readRolesPermission->id, 'position' => 1],
                ['destiny' => 'permissions.index', 'module_id' => $modulePermission->id, 'name' => json_encode(['en' => 'Permissions', 'es' => 'Permisos']), 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readPermissionsPermission->id, 'position' => 2],
            ], ['destiny', 'module_id'], ['name', 'type', 'parent', 'permission_id', 'position']);
        }

        // Settings
        if ($settingsParent = Navigation::updateOrCreate(['destiny' => '#navigation-settings', 'module_id' => null], ['name' => 'Settings', 'type' => 'custom', 'parent' => 0, 'permission_id' => 0, 'position' => 2])->setLocale('en')) {
            // Spanish name update
            $settingsParent->setLocale('es');
            $settingsParent->name = 'Navegación';
            $settingsParent->save();
            // Children
            Navigation::upsert([
                ['destiny' => 'loggers.index', 'module_id' => null, 'name' => json_encode(['en' => 'Logger', 'es' => 'Logger']), 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $readLoggersPermission->id, 'position' => 2],
                ['destiny' => 'configs.index', 'module_id' => $moduleConfig->id, 'name' => json_encode(['en' => 'Config', 'es' => 'Configuración']), 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $readConfigsPermission->id, 'position' => 0],
                ['destiny' => 'navigations.index', 'module_id' => $moduleNavigation->id, 'name' => json_encode(['en' => 'Navigation', 'es' => 'Navegación']), 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $readNavigationsPermission->id, 'position' => 1],
            ], ['destiny', 'module_id'], ['name', 'type', 'parent', 'permission_id', 'position']);
        }
    }
}
