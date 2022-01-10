<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Traits\PbModelCrudTrait;
use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use Spatie\Permission\Models\Role;
use Spatie\Translatable\HasTranslations;

class PbRole extends Role
{
    use HasTranslations;
    use PbModelTrait;
    use PbModelCrudTrait;

    protected $connection;

    public $translatable = ['alias'];

    protected $appends = ['crud'];

    function __construct() {
        $this->connection = config('database.default');
        $this->publicRelations = ['permissions'];
        $this->allRelations = ['permissions'];
    }

    public function getAliasAttribute($value)
    {
        return PbHelpers::translateString($value);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return array
     */
    public static function getCrudConfig(): array
    {
        $config = PbBuilder::getCrudConfig();

        $config['fields'] = [
            'name' => [],
            'alias' => [],
        ];

        $config['formconfig'] = [
            'name' => [
                'type' => 'text',
            ],
            'alias' => [
                'type' => 'text',
            ],
            'permissions' => [
                'type' => 'select-multiple',
                'list' => Shares::getPermissionsAll()['permissionsall']->toArray(),
            ],
        ];

        $config['relations'] = ['permissions'];

        return $config;
    }
}
