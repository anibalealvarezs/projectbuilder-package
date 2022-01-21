<?php

namespace Anibalealvarezs\Projectbuilder\Commands;

use Illuminate\Console\Command;

class PbHelpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbuilder:help';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project Builder help';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            echo "\n";
            echo "[[ Available commands ]]\n\n";
            echo ">> pbuilder:install\n";
            echo "For package installation\n\n";
            echo ">> pbuilder:altinstall\n";
            echo "For package installation via 'shell_exec' instead of 'Artisan::call()' method\n\n";
            echo "[[ Parameters ]]\n\n";
            echo ">> --all      |  For performing all tasks below\n";
            echo ">> --inertia  |  For installing Jetstream and Inertia\n";
            echo ">> --publish  |  For publishing resources\n";
            echo ">> --migrate  |  For running migrations\n";
            echo ">> --seed     |  For seeding the database\n";
            echo ">> --config   |  For configuring the application\n";
            echo ">> --link     |  For creating links\n";
            echo ">> --npm      |  For requiring npm resources\n";
            echo ">> --compile  |  For compiling webpack\n";
            echo ">> --force  |  For resetting database on migration\n";
        } catch (Exception $e) {
            echo "-- [[ ERROR: ".$e->getMessage()." ]]";
        }
    }
}
