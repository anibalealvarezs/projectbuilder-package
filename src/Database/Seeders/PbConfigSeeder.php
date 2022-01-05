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
            ['configkey' => '_APP_NAME_', 'configvalue' => 'Builder', 'name' => json_encode(['en' => 'App Name']), 'description' => json_encode(['en' => 'App Description']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_FORCE_HTTPS_', 'configvalue' => false, 'name' => json_encode(['en' => 'Force HTTPS']), 'description' => json_encode(['en' => 'Force HTTPS']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_SAVE_LOGS_', 'configvalue' => false, 'name' => json_encode(['en' => 'Save Logs']), 'description' => json_encode(['en' => 'Save Logs']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_API_ENABLED_', 'configvalue' => true, 'name' => json_encode(['en' => 'API Enabled']), 'description' => json_encode(['en' => 'API Enabled']), 'module_id' => $moduleConfig->id],
            ['configkey' => '_DEBUG_MODE_', 'configvalue' => true, 'name' => json_encode(['en' => 'Debug Mode']), 'description' => json_encode(['en' => 'Debug Mode']), 'module_id' => $moduleConfig->id]
        ], ['configkey'], ['configvalue', 'name', 'description', 'module_id']);
    }
}
