<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class PbLogger extends Model
{
    use HasRoles;
    use HasTranslations;

    public $translatable = ['message'];

    protected $table = 'logger';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'severity', 'code', 'message', 'object_type', 'object_id', 'user_id'
    ];

    public function getMessageAttribute($value)
    {
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
        }
        return $value;
    }
}
