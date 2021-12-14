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
}
