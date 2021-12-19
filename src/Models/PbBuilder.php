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

    public static function getCrudConfig(): array
    {
        $config['default'] = [];
        $config['relations'] = [];
        $config['action_routes'] = [];
        $config['enabled_actions'] = [
            'update' => true,
            'delete' => true,
            'create' => true,
        ];
        $config['custom_order'] = [];

        return $config;
    }
}
