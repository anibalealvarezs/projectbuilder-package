<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Spatie\Translatable\HasTranslations;

class PbModule extends PbBuilder
{
    use HasTranslations;

    protected $table = 'modules';

    public $translatable = ['name'];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'modulekey', 'status'
    ];

    public function getNameAttribute($value)
    {
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
        }
        return $value;
    }
}
