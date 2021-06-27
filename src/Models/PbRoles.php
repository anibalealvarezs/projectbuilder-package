<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Spatie\Permission\Models\Role;

class PbRoles extends Role
{
    // use ModelTrait;

    protected $connection;

    function __construct() {
        $this->connection = config('database.default');
    }

}
