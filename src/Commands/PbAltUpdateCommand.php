<?php

namespace Anibalealvarezs\Projectbuilder\Commands;

class PbAltUpdateCommand extends PbAltInstallCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbuilder:altupdate
                            {--all : All tasks will be performed}
                            {--inertia : Includes Jetstream and Inertia installation}
                            {--publish : Resources will be published to the application}
                            {--migrate : Migrations will be run}
                            {--seed : Tables will be seeded}
                            {--config : Application wil be configured}
                            {--link : Links will be created}
                            {--npm : npm resources will be required}
                            {--compile : npm will be run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project Builder alternative updating';
}
