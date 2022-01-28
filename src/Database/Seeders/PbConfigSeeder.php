<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbConfig as Config;
use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Illuminate\Database\Seeder;

class PbConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $moduleConfig = PbModule::where('modulekey', 'config')->first();
        // Default Config
        Config::upsert([
            ['configkey' => '_APP_NAME_', 'configvalue' => 'Builder', 'name' => json_encode(['en' => 'App Name', 'es' => 'Nombre de la Aplicación']), 'description' => json_encode(['en' => 'App Description', 'es' => 'Nombre de la Aplicación']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_FORCE_HTTPS_', 'configvalue' => false, 'name' => json_encode(['en' => 'Force HTTPS', 'es' => 'Forzar HTTPS']), 'description' => json_encode(['en' => 'Force HTTPS', 'es' => 'Forzar HTTPS']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_SAVE_LOGS_', 'configvalue' => false, 'name' => json_encode(['en' => 'Save Logs', 'es' => 'Guardar Logs']), 'description' => json_encode(['en' => 'Save Logs', 'es' => 'Habilitar el registro de eventos en el log']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_API_ENABLED_', 'configvalue' => true, 'name' => json_encode(['en' => 'API Enabled', 'es' => 'API Habilitada']), 'description' => json_encode(['en' => 'API Enabled', 'es' => 'Habilitar el acceso vía API']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_DEBUG_MODE_', 'configvalue' => true, 'name' => json_encode(['en' => 'Debug Mode', 'es' => 'Modo Debug']), 'description' => json_encode(['en' => 'Debug Mode', 'es' => 'Habilitar la barra de debug']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_TEAM_SELECTOR_', 'configvalue' => false, 'name' => json_encode(['en' => 'Team Selector', 'es' => 'Selector de Equipo']), 'description' => json_encode(['en' => 'Show the team selector', 'es' => 'Mostrar el selector de equipo']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_ENABLE_SINGLE_SESSION_', 'configvalue' => false, 'name' => json_encode(['en' => 'Enable Single Session', 'es' => 'Habilitar Sesión Única']), 'description' => json_encode(['en' => 'Allow a single session by user', 'es' => 'Permitir una sola sesión por usuario']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_FORCE_NEW_SESSION_', 'configvalue' => false, 'name' => json_encode(['en' => 'New login session force', 'es' => 'Forzado se sesión en nuevo login']), 'description' => json_encode(['en' => 'Allow new logging user to force session start', 'es' => 'Permitir al usuario que se loguea forzar el inicio de sesión']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_ALLOW_MULTIPLE_ADMIN_SESSION_', 'configvalue' => false, 'name' => json_encode(['en' => 'Allow admin multiple sessions', 'es' => 'Permitir sesiones múltiples de administradores']), 'description' => json_encode(['en' => 'Allow admin multiple sessions', 'es' => 'Permitir sesiones múltiples de administradores']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_DISABLE_PASSWORD_RESET_', 'configvalue' => false, 'name' => json_encode(['en' => 'Disable password resetting', 'es' => 'Deshabilitar reseteo de contraseña']), 'description' => json_encode(['en' => 'Prevent users from resetting their password if not authenticated', 'es' => 'Impedir que los usuarios puedan resetear su clave sin estar autenticados']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_DISABLE_LOGIN_', 'configvalue' => false, 'name' => json_encode(['en' => 'Disable login form', 'es' => 'Deshabilitar formulario de ingreso']), 'description' => json_encode(['en' => 'Disable the login form', 'es' => 'Deshabilitar el formulario de ingreso']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_DISABLE_REGISTER_', 'configvalue' => false, 'name' => json_encode(['en' => 'Disable registration form', 'es' => 'Deshabilitar formulario de registro']), 'description' => json_encode(['en' => 'Disable the registration form', 'es' => 'Deshabilitar el formulario de registro']), 'module_id' => $moduleConfig->id],
        ], ['configkey'], ['configvalue', 'name', 'description', 'module_id']);
    }
}
