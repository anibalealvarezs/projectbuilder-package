<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Traits\PbModelEnableableTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class PbLanguage extends PbBuilder
{
    use PbModelEnableableTrait;

    protected $table = 'languages';

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
        $this->publicRelations = ['cities', 'countries'];
        $this->allRelations = ['users', 'cities', 'countries'];
        $this->translatable = ['name'];
        $this->appends = [...$this->appends, ...['names']];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'status'
    ];

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function scopeDisable(): bool
    {
        // Remove language from users
        PbUser::removeLanguage($this);

        return $this->update(['status' => false]);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return void
     */
    public function putCountry()
    {
        $this->country = getDefaultCountry($this->code);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function getNameAttribute($value)
    {
        return translateString($value);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $code
     * @return PbLanguage|null
     */
    public static function findByCode($code): self|null
    {
        return self::firstWhere('code', $code);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return MorphToMany
     */
    public function cities(): MorphToMany
    {
        return $this->morphedByMany(PbCity::class, 'langable');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return MorphToMany
     */
    public function countries(): MorphToMany
    {
        return $this->morphedByMany(PbCountry::class, 'langable');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(PbUser::class);
    }
}
