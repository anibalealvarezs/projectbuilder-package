<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class PbNavigation extends Model
{
    use ModelTrait;

    protected $table = 'navigations';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'destiny', 'type', 'parent', 'module'
    ];
}
