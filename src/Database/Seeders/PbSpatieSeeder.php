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

        $moduleUser = PbModule::whereModulekey('user')->first();
        $moduleConfig = PbModule::whereModulekey('config')->first();
        $moduleNavigation = PbModule::whereModulekey('navigation')->first();
        $moduleRole = PbModule::whereModulekey('role')->first();
        $modulePermission = PbModule::whereModulekey('permission')->first();

        Permission::query()->delete();
        Role::query()->delete();

        // upsert permissions
        Permission::upsert([
            // CRUD Super Admin
            ['guard_name' => 'admin', 'name' => 'crud super-admin', 'alias' => json_encode(['en' => 'CRUD Super Admin', 'es' => 'CRUD Super Admin']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'admin roles permissions', 'alias' => json_encode(['en' => 'Admin Roles & Permissions', 'es' => 'Adminisrar Roles y Permisos']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'manage app', 'alias' => json_encode(['en' => 'Manage App', 'es' => 'Adminisrar Aplicación']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'login', 'alias' => json_encode(['en' => 'Login', 'es' => 'Iniciar Sesión']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'config builder', 'alias' => json_encode(['en' => 'Edit main config', 'es' => 'Editar configuración principal']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'developer options', 'alias' => json_encode(['en' => 'Developer Options', 'es' => 'Opciones de Desarrollador']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'api access', 'alias' => json_encode(['en' => 'API Access', 'es' => 'Acceso API']), 'module_id' => null],
            // CRUD Logger
            ['guard_name' => 'admin', 'name' => 'config loggers', 'alias' => json_encode(['en' => 'Configure Logger', 'es' => 'Configurar Logger']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'create loggers', 'alias' => json_encode(['en' => 'Create Logs', 'es' => 'Crear Logs']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'read loggers', 'alias' => json_encode(['en' => 'Read Logs', 'es' => 'Leer Logs']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'update loggers', 'alias' => json_encode(['en' => 'Update Logs', 'es' => 'Actualizar Logs']), 'module_id' => null],
            ['guard_name' => 'admin', 'name' => 'delete loggers', 'alias' => json_encode(['en' => 'Delete Logs', 'es' => 'Borrar Logs']), 'module_id' => null],
            // CRUD Users
            ['guard_name' => 'admin', 'name' => 'config users', 'alias' => json_encode(['en' => 'Configure Users', 'es' => 'Configurar Usuarios']), 'module_id' => $moduleUser->id],
            ['guard_name' => 'admin', 'name' => 'create users', 'alias' => json_encode(['en' => 'Create Users', 'es' => 'Crear Usuarios']), 'module_id' => $moduleUser->id],
            ['guard_name' => 'admin', 'name' => 'read users', 'alias' => json_encode(['en' => 'Read Users', 'es' => 'Leer Usuarios']), 'module_id' => $moduleUser->id],
            ['guard_name' => 'admin', 'name' => 'update users', 'alias' => json_encode(['en' => 'Update Users', 'es' => 'Actualizar Usuarios']), 'module_id' => $moduleUser->id],
            ['guard_name' => 'admin', 'name' => 'delete users', 'alias' => json_encode(['en' => 'Delete Users', 'es' => 'Borrar Usuarios']), 'module_id' => $moduleUser->id],
            // CRUD Configs
            ['guard_name' => 'admin', 'name' => 'config configs', 'alias' => json_encode(['en' => 'Configure Configs', 'es' => 'Configurar Configuraciones']), 'module_id' => $moduleConfig->id],
            ['guard_name' => 'admin', 'name' => 'create configs', 'alias' => json_encode(['en' => 'Create Configs', 'es' => 'Crear Configuraciones']), 'module_id' => $moduleConfig->id],
            ['guard_name' => 'admin', 'name' => 'read configs', 'alias' => json_encode(['en' => 'Read Configs', 'es' => 'Leer Configuraciones']), 'module_id' => $moduleConfig->id],
            ['guard_name' => 'admin', 'name' => 'update configs', 'alias' => json_encode(['en' => 'Update Configs', 'es' => 'Actualizar Configuraciones']), 'module_id' => $moduleConfig->id],
            ['guard_name' => 'admin', 'name' => 'delete configs', 'alias' => json_encode(['en' => 'Delete Configs', 'es' => 'Borrar Configuraciones']), 'module_id' => $moduleConfig->id],
            // CRUD Navigations
            ['guard_name' => 'admin', 'name' => 'config navigations', 'alias' => json_encode(['en' => 'Configure Navigations', 'es' => 'Configurar Navegaciones']), 'module_id' => $moduleNavigation->id],
            ['guard_name' => 'admin', 'name' => 'create navigations', 'alias' => json_encode(['en' => 'Create Navigations', 'es' => 'Crear Navegaciones']), 'module_id' => $moduleNavigation->id],
            ['guard_name' => 'admin', 'name' => 'read navigations', 'alias' => json_encode(['en' => 'Read Navigations', 'es' => 'Leer Navegaciones']), 'module_id' => $moduleNavigation->id],
            ['guard_name' => 'admin', 'name' => 'update navigations', 'alias' => json_encode(['en' => 'Update Navigations', 'es' => 'Actualizar Navegaciones']), 'module_id' => $moduleNavigation->id],
            ['guard_name' => 'admin', 'name' => 'delete navigations', 'alias' => json_encode(['en' => 'Delete Navigations', 'es' => 'Borrar Navegaciones']), 'module_id' => $moduleNavigation->id],
            // CRUD Permissions
            ['guard_name' => 'admin', 'name' => 'config permissions', 'alias' => json_encode(['en' => 'Configure Permissions', 'es' => 'Configurar Permisos']), 'module_id' => $modulePermission->id],
            ['guard_name' => 'admin', 'name' => 'create permissions', 'alias' => json_encode(['en' => 'Create Permissions', 'es' => 'Crear Permisos']), 'module_id' => $modulePermission->id],
            ['guard_name' => 'admin', 'name' => 'read permissions', 'alias' => json_encode(['en' => 'Read Permissions', 'es' => 'Leer Permisos']), 'module_id' => $modulePermission->id],
            ['guard_name' => 'admin', 'name' => 'update permissions', 'alias' => json_encode(['en' => 'Update Permissions', 'es' => 'Actualizar Permisos']), 'module_id' => $modulePermission->id],
            ['guard_name' => 'admin', 'name' => 'delete permissions', 'alias' => json_encode(['en' => 'Delete Permissions', 'es' => 'Borrar Permisos']), 'module_id' => $modulePermission->id],
            // CRUD Roles
            ['guard_name' => 'admin', 'name' => 'config roles', 'alias' => json_encode(['en' => 'Configure Roles', 'es' => 'Configurar Roles']), 'module_id' => $moduleRole->id],
            ['guard_name' => 'admin', 'name' => 'create roles', 'alias' => json_encode(['en' => 'Create Roles', 'es' => 'Crear Roles']), 'module_id' => $moduleRole->id],
            ['guard_name' => 'admin', 'name' => 'read roles', 'alias' => json_encode(['en' => 'Read Roles', 'es' => 'Leer Roles']), 'module_id' => $moduleRole->id],
            ['guard_name' => 'admin', 'name' => 'update roles', 'alias' => json_encode(['en' => 'Update Roles', 'es' => 'Actualizar Roles']), 'module_id' => $moduleRole->id],
            ['guard_name' => 'admin', 'name' => 'delete roles', 'alias' => json_encode(['en' => 'Delete Roles', 'es' => 'Borrar Roles']), 'module_id' => $moduleRole->id]
        ], ['guard_name', 'name'], ['alias', 'module_id']);

        // updateOrCreate roles and assign updateOrCreated permissions
        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'user'], ['guard_name' => 'admin', 'name' => 'user', 'alias' => ['en' => 'User', 'es' => 'Usuario']])
            ->givePermissionTo(['login']);
        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'api-user'], ['guard_name' => 'admin', 'name' => 'api-user', 'alias' => ['en' => 'API User', 'es' => 'Usuario API']])
            ->givePermissionTo(['api access']);
        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'admin'], ['guard_name' => 'admin', 'name' => 'admin', 'alias' => ['en' => 'Admin', 'es' => 'Administrador']])
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
        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'developer'], ['guard_name' => 'admin', 'name' => 'developer', 'alias' => ['en' => 'Developer', 'es' => 'Desarrollador']])
            ->givePermissionTo(['developer options', 'read loggers', 'delete loggers', 'config loggers']);
        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'super-admin'], ['guard_name' => 'admin', 'name' => 'super-admin', 'alias' => ['en' => 'Super Admin', 'es' => 'Superadministrador']])
            ->givePermissionTo(Permission::all());
    }
}
