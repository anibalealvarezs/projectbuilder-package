<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use App\Providers\JetstreamServiceProvider;
use Laravel\Jetstream\Jetstream;

class PbJetstreamAppServiceProvider extends JetstreamServiceProvider
{
    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['create', 'read', 'update', 'delete']);
    }
}
