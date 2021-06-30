<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PbNavigation extends Model
{
    use PbModelTrait;
    use HasTranslations;

    protected $table = 'navigations';

    public $translatable = ['name'];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'destiny', 'type', 'parent', 'module', ''
    ];

    public function getNameAttribute($value)
    {
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
        }
        return $value;
    }

    public function ascendant(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent', 'id')->with('ascendant');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(PbPermission::class);
    }

    public function descendants(): HasMany
    {
        // Recursive Relationship
        return $this->hasMany(self::class, 'parent', 'id')->with('descendants');
    }
}
