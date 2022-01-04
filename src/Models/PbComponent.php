<?php

namespace Anibalealvarezs\Projectbuilder\Models;

class PbComponent extends PbBuilder
{
    protected $table = 'components';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'module', 'path'
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(PbModule::class, 'module', 'modulekey');
    }

    public static function exists($name, $module): bool
    {
        $module = self::where('name', $name)->where('module', $module)->first();
        if ($module) {
            return true;
        }
        return false;
    }
}
