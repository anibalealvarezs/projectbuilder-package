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

    public function getByKey($value): bool
    {
        $module = self::where('modulekey', $value)->first();
        if ($module) {
            return self::find($module->id);
        }
        return false;
    }

    public static function isEnabled($value): bool
    {
        $module = (new PbModule)->getByKey($value);
        if ($module) {
            return (bool) $module->status;
        }
        return false;
    }

    public static function exists($value): bool
    {
        $module = (new PbModule)->getByKey($value);
        if ($module) {
            return true;
        }
        return false;
    }
}
