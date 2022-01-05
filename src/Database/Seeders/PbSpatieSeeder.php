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

        Permission::query()->delete();
        Role::query()->delete();

        // upsert permissions
        Permission::upsert([
            // CRUD Super Admin
            ['guard_name' => 'admin', 'name' => 'crud super-admin', 'alias' => json_encode(['en' => 'CRUD Super Admin']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'admin roles permissions', 'alias' => json_encode(['en' => 'Admin Roles & Permissions']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'manage app', 'alias' => json_encode(['en' => 'Manage App']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'login', 'alias' => json_encode(['en' => 'Login']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'config builder', 'alias' => json_encode(['en' => 'Edit main config variables']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'developer options', 'alias' => json_encode(['en' => 'Developer Options']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'api access', 'alias' => json_encode(['en' => 'API Access']), 'module_id' => null],
            // CRUD Logger
            ['guard_name' => 'admin', 'name' => 'config loggers', 'alias' => json_encode(['en' => 'Configure Logger']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'create loggers', 'alias' => json_encode(['en' => 'Create Logs']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'read loggers', 'alias' => json_encode(['en' => 'Read Logs']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'update loggers', 'alias' => json_encode(['en' => 'Update Logs']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'delete loggers', 'alias' => json_encode(['en' => 'Delete Logs']), 'module_id' => null],
            // CRUD Users
            ['guard_name' => 'admin', 'name' => 'config users', 'alias' => json_encode(['en' => 'Configure Users']), 'module_id' => $moduleUser->id],
            ['guard_name' => 'admin', 'name' => 'create users', 'alias' => json_encode(['en' => 'Create Users']), 'module_id' => $moduleUser->id],
            ['guard_name' => 'admin', 'name' => 'read users', 'alias' => json_encode(['en' => 'Read Users']), 'module_id' => $moduleUser->id],
            ['guard_name' => 'admin', 'name' => 'update users', 'alias' => json_encode(['en' => 'Update Users']), 'module_id' => $moduleUser->id],
            ['guard_name' => 'admin', 'name' => 'delete users', 'alias' => json_encode(['en' => 'Delete Users']), 'module_id' => $moduleUser->id],
            // CRUD Configs
            ['guard_name' => 'admin', 'name' => 'config configs', 'alias' => json_encode(['en' => 'Configure Configs']), 'module_id' => $moduleConfig->id],
            ['guard_name' => 'admin', 'name' => 'create configs', 'alias' => json_encode(['en' => 'Create Configs']), 'module_id' => $moduleConfig->id],
            ['guard_name' => 'admin', 'name' => 'read configs', 'alias' => json_encode(['en' => 'Read Configs']), 'module_id' => $moduleConfig->id],
            ['guard_name' => 'admin', 'name' => 'update configs', 'alias' => json_encode(['en' => 'Update Configs']), 'module_id' => $moduleConfig->id],
            ['guard_name' => 'admin', 'name' => 'delete configs', 'alias' => json_encode(['en' => 'Delete Configs']), 'module_id' => $moduleConfig->id],
            // CRUD Navigations
            ['guard_name' => 'admin', 'name' => 'config navigations', 'alias' => json_encode(['en' => 'Configure Navigations']), 'module_id' => $moduleNavigation->id],
            ['guard_name' => 'admin', 'name' => 'create navigations', 'alias' => json_encode(['en' => 'Create Navigations']), 'module_id' => $moduleNavigation->id],
            ['guard_name' => 'admin', 'name' => 'read navigations', 'alias' => json_encode(['en' => 'Read Navigations']), 'module_id' => $moduleNavigation->id],
            ['guard_name' => 'admin', 'name' => 'update navigations', 'alias' => json_encode(['en' => 'Update Navigations']), 'module_id' => $moduleNavigation->id],
            ['guard_name' => 'admin', 'name' => 'delete navigations', 'alias' => json_encode(['en' => 'Delete Navigations']), 'module_id' => $moduleNavigation->id],
            // CRUD Permissions
            ['guard_name' => 'admin', 'name' => 'config permissions', 'alias' => json_encode(['en' => 'Configure Permissions']), 'module_id' => $modulePermission->id],
            ['guard_name' => 'admin', 'name' => 'create permissions', 'alias' => json_encode(['en' => 'Create Permissions']), 'module_id' => $modulePermission->id],
            ['guard_name' => 'admin', 'name' => 'read permissions', 'alias' => json_encode(['en' => 'Read Permissions']), 'module_id' => $modulePermission->id],
            ['guard_name' => 'admin', 'name' => 'update permissions', 'alias' => json_encode(['en' => 'Update Permissions']), 'module_id' => $modulePermission->id],
            ['guard_name' => 'admin', 'name' => 'delete permissions', 'alias' => json_encode(['en' => 'Delete Permissions']), 'module_id' => $modulePermission->id],
            // CRUD Roles
            ['guard_name' => 'admin', 'name' => 'config roles', 'alias' => json_encode(['en' => 'Configure Roles']), 'module_id' => $moduleRole->id],
            ['guard_name' => 'admin', 'name' => 'create roles', 'alias' => json_encode(['en' => 'Create Roles']), 'module_id' => $moduleRole->id],
            ['guard_name' => 'admin', 'name' => 'read roles', 'alias' => json_encode(['en' => 'Read Roles']), 'module_id' => $moduleRole->id],
            ['guard_name' => 'admin', 'name' => 'update roles', 'alias' => json_encode(['en' => 'Update Roles']), 'module_id' => $moduleRole->id],
            ['guard_name' => 'admin', 'name' => 'delete roles', 'alias' => json_encode(['en' => 'Delete Roles']), 'module_id' => $moduleRole->id]
        ], ['guard_name', 'name'], ['alias', 'module_id']);

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
