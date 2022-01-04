<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function components(): HasMany
    {
        return $this->hasMany(PbComponent::class, 'module', 'modulekey');
    }

    public function getByKey($value): bool
    {
        return self::firstWhere('modulekey', $value);
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
