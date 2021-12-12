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
            echo "[[ Available commands ]]\n\n";
            echo ">> pbuilder:install\n";
            echo "For package installation\n\n";
            echo ">> pbuilder:install --inertia\n";
            echo "For package installation including Jetstream and Inertia\n\n";
        } catch (Exception $e) {
            echo "-- [[ ERROR: ".$e->getMessage()." ]]";
        }
    }
}
