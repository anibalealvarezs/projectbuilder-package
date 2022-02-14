<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class PbModule extends PbBuilder
{
    protected $table = 'modules';

    public $timestamps = false;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->publicRelations = ['components'];
        $this->allRelations = ['components'];
        $this->translatable = ['name'];
        $this->appends = [...$this->appends, ...['names']];
    }

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
        return translateString($value);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return HasMany
     */
    public function components(): HasMany
    {
        return $this->hasMany(PbComponent::class, 'module', 'modulekey');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $value
     * @return PbModule|null
     */
    public static function getByKey($value): self|null
    {
        return self::firstWhere('modulekey', $value);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '1');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $value
     * @return bool
     */
    public static function isEnabled($value): bool
    {
        if ($module = self::getByKey($value)) {
            return (bool) $module->status;
        }
        return false;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $value
     * @return bool
     */
    public static function exists($value): bool
    {
        if (self::getByKey($value)) {
            return true;
        }
        return false;
    }
}
