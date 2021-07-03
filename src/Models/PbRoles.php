<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use Spatie\Permission\Models\Role;
use Spatie\Translatable\HasTranslations;

class PbRoles extends Role
{
    use HasTranslations;
    use PbModelTrait;

    protected $connection;

    public $translatable = ['alias'];

    function __construct() {
        $this->connection = config('database.default');
    }

    public function getAliasAttribute($value)
    {
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
        }
        return $value;
    }

}
