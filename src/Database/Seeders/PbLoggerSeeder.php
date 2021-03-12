<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbLogger as Logger;
use Illuminate\Database\Seeder;

class PbLoggerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // First Log Message
        Logger::create(['message' => 'App Created']);
    }
}
