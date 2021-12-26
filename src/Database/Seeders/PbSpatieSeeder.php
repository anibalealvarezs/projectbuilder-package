<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Anibalealvarezs\Projectbuilder\Models\PbPermission as Permission;
use Anibalealvarezs\Projectbuilder\Models\PbRole as Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PbSpatieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $moduleUser = PbModule::where('modulekey', 'user')->first();
        $moduleConfig = PbModule::where('modulekey', 'config')->first();
        $moduleNavigation = PbModule::where('modulekey', 'navigation')->first();
        $moduleRole = PbModule::where('modulekey', 'role')->first();
        $modulePermission = PbModule::where('modulekey', 'permission')->first();

        // updateOrCreate permissions
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'crud super-admin'],
            ['guard_name' => 'admin', 'name' => 'crud super-admin', 'alias' => 'CRUD Super Admin', 'module_id' => null]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'admin roles permissions'],
            ['guard_name' => 'admin', 'name' => 'admin roles permissions', 'alias' => 'Admin Roles & Permissions', 'module_id' => null]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'manage app'],
            ['guard_name' => 'admin', 'name' => 'manage app', 'alias' => 'Manage App', 'module_id' => null]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'login'],
            ['guard_name' => 'admin', 'name' => 'login', 'alias' => 'Login', 'module_id' => null]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'config builder'],
            ['guard_name' => 'admin', 'name' => 'config builder', 'alias' => 'Edit main config variables', 'module_id' => null]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'developer options'],
            ['guard_name' => 'admin', 'name' => 'developer options', 'alias' => 'Developer Options', 'module_id' => null]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'api access'],
            ['guard_name' => 'admin', 'name' => 'api access', 'alias' => 'API Access', 'module_id' => null]);
        // CRUD Logger
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'config loggers'],
            ['guard_name' => 'admin', 'name' => 'config loggers', 'alias' => 'Configure Logger', 'module_id' => null]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'create loggers'],
            ['guard_name' => 'admin', 'name' => 'create loggers', 'alias' => 'Create Logs', 'module_id' => null]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'read loggers'],
            ['guard_name' => 'admin', 'name' => 'read loggers', 'alias' => 'Read Logs', 'module_id' => null]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'update loggers'],
            ['guard_name' => 'admin', 'name' => 'update loggers', 'alias' => 'Update Logs', 'module_id' => null]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'delete loggers'],
            ['guard_name' => 'admin', 'name' => 'delete loggers', 'alias' => 'Delete Logs', 'module_id' => null]);
        // CRUD Users
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'config users'],
            ['guard_name' => 'admin', 'name' => 'config users', 'alias' => 'Configure Users', 'module_id' => $moduleUser->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'create users'],
            ['guard_name' => 'admin', 'name' => 'create users', 'alias' => 'Create Users', 'module_id' => $moduleUser->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'read users'],
            ['guard_name' => 'admin', 'name' => 'read users', 'alias' => 'Read Users', 'module_id' => $moduleUser->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'update users'],
            ['guard_name' => 'admin', 'name' => 'update users', 'alias' => 'Update Users', 'module_id' => $moduleUser->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'delete users'],
            ['guard_name' => 'admin', 'name' => 'delete users', 'alias' => 'Delete Users', 'module_id' => $moduleUser->id]);
        // CRUD Configs
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'config configs'],
            ['guard_name' => 'admin', 'name' => 'config configs', 'alias' => 'Configure Configs', 'module_id' => $moduleConfig->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'create configs'],
            ['guard_name' => 'admin', 'name' => 'create configs', 'alias' => 'Create Configs', 'module_id' => $moduleConfig->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'read configs'],
            ['guard_name' => 'admin', 'name' => 'read configs', 'alias' => 'Read Configs', 'module_id' => $moduleConfig->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'update configs'],
            ['guard_name' => 'admin', 'name' => 'update configs', 'alias' => 'Update Configs', 'module_id' => $moduleConfig->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'delete configs'],
            ['guard_name' => 'admin', 'name' => 'delete configs', 'alias' => 'Delete Configs', 'module_id' => $moduleConfig->id]);
        // CRUD Navigations
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'config navigations'],
            ['guard_name' => 'admin', 'name' => 'config navigations', 'alias' => 'Configure Navigations', 'module_id' => $moduleNavigation->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'create navigations'],
            ['guard_name' => 'admin', 'name' => 'create navigations', 'alias' => 'Create Navigations', 'module_id' => $moduleNavigation->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'read navigations'],
            ['guard_name' => 'admin', 'name' => 'read navigations', 'alias' => 'Read Navigations', 'module_id' => $moduleNavigation->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'update navigations'],
            ['guard_name' => 'admin', 'name' => 'update navigations', 'alias' => 'Update Navigations', 'module_id' => $moduleNavigation->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'delete navigations'],
            ['guard_name' => 'admin', 'name' => 'delete navigations', 'alias' => 'Delete Navigations', 'module_id' => $moduleNavigation->id]);
        // CRUD Permissions
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'config permissions'],
            ['guard_name' => 'admin', 'name' => 'config permissions', 'alias' => 'Configure Permissions', 'module_id' => $modulePermission->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'create permissions'],
            ['guard_name' => 'admin', 'name' => 'create permissions', 'alias' => 'Create Permissions', 'module_id' => $modulePermission->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'read permissions'],
            ['guard_name' => 'admin', 'name' => 'read permissions', 'alias' => 'Read Permissions', 'module_id' => $modulePermission->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'update permissions'],
            ['guard_name' => 'admin', 'name' => 'update permissions', 'alias' => 'Update Permissions', 'module_id' => $modulePermission->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'delete permissions'],
            ['guard_name' => 'admin', 'name' => 'delete permissions', 'alias' => 'Delete Permissions', 'module_id' => $modulePermission->id]);
        // CRUD Roles
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'config roles'],
            ['guard_name' => 'admin', 'name' => 'config roles', 'alias' => 'Configure Roles', 'module_id' => $moduleRole->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'create roles'],
            ['guard_name' => 'admin', 'name' => 'create roles', 'alias' => 'Create Roles', 'module_id' => $moduleRole->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'read roles'],
            ['guard_name' => 'admin', 'name' => 'read roles', 'alias' => 'Read Roles', 'module_id' => $moduleRole->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'update roles'],
            ['guard_name' => 'admin', 'name' => 'update roles', 'alias' => 'Update Roles', 'module_id' => $moduleRole->id]);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'delete roles'],
            ['guard_name' => 'admin', 'name' => 'delete roles', 'alias' => 'Delete Roles', 'module_id' => $moduleRole->id]);

        // updateOrCreate roles and assign updateOrCreated permissions

        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'user'], ['guard_name' => 'admin', 'name' => 'user', 'alias' => 'User'])
            ->givePermissionTo(['login']);
        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'api-user'], ['guard_name' => 'admin', 'name' => 'api-user', 'alias' => 'API User'])
            ->givePermissionTo(['api access']);
        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'admin'], ['guard_name' => 'admin', 'name' => 'admin', 'alias' => 'Admin'])
            ->givePermissionTo([
                'create users',
                'read users',
                'update users',
                'delete users',
                'read configs',
                'update configs',
                'read permissions',
                'read roles',
                'manage app',
                'admin roles permissions',
                'config builder',
                'login',
            ]);
        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'developer'], ['guard_name' => 'admin', 'name' => 'developer', 'alias' => 'Developer'])
            ->givePermissionTo(['developer options', 'read loggers', 'delete loggers', 'config loggers']);
        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'super-admin'], ['guard_name' => 'admin', 'name' => 'super-admin', 'alias' => 'Super Admin'])
            ->givePermissionTo(Permission::all());
    }
}
