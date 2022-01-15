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
        ], ['configkey'], ['configvalue', 'name', 'description', 'module_id']);
    }
}
