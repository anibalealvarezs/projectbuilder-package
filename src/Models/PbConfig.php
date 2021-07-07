<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;

class PbConfig extends PbBuilder
{
    use HasTranslations;

    protected $table = 'config';

    public $translatable = ['name', 'description'];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'configkey', 'configvalue', 'name', 'description'
    ];

    public function getNameAttribute($value)
    {
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
        }
        return $value;
    }

    public function getDescriptionAttribute($value)
    {
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
        }
        return $value;
    }

    public static function findByKey($key)
    {
        $config = DB::table('config')
                ->select('id')
                ->where('configkey', $key)
                ->first();
        if ($config->id) {
            return self::find($config->id);
        }
        return null;
    }
}
