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

    protected $appends = ['crud'];

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

    public function isEditableBy($id): bool
    {
        return true;
    }

    public function isViewableBy($id): bool
    {
        return true;
    }

    public function isSelectableBy($id): bool
    {
        return true;
    }

    public function isDeletableBy($id): bool
    {
        return true;
    }

}
