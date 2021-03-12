<?php

namespace Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbConfig;
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
        // Default Config
        PbConfig::create(['key' => '_APP_NAME_', 'value' => 'Builder']);
        PbConfig::create(['key' => '_FORCE_HTTPS_', 'value' => false]);
    }
}
