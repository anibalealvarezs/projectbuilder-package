<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Traits\PbModelCrudTrait;
use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use Illuminate\Database\Eloquent\Model;

class PbBuilder extends Model
{
    use PbModelTrait;
    use PbModelCrudTrait;

    protected $appends = ['crud'];

    /**
     * Scope a query to only include popular users.
     *
     * @return array
     */
    public static function getCrudConfig(): array
    {
        return [
            'default' => [],
            'relations' => [],
            'action_routes' => [],
            'enabled_actions' => [
                'update' => true,
                'delete' => true,
                'create' => true,
            ],
            'custom_order' => [],
        ];
    }
}
