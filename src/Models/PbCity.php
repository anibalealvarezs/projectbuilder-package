<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Translatable\HasTranslations;

class PbCity extends PbBuilder
{
    use HasTranslations;

    protected $table = 'cities';

    public $translatable = ['name'];

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
        $this->publicRelations = ['country', 'langs'];
        $this->allRelations = ['country', 'langs', 'users'];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function getNameAttribute($value)
    {
        return PbHelpers::translateString($value);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function delete(): bool
    {
        // Remove langs relations
        $this->langs()->detach();

        // Remove cities from users
        PbUser::where('city_id', $this->id)->update(['city_id' => null]);

        // Remove cities from users
        PbCountry::where('capital_id', $this->id)->update(['capital_id' => null]);

        // delete the user
        return parent::delete();
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(PbCountry::class);
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

    /**
     * Scope a query to only include popular users.
     *
     * @return MorphToMany
     */
    public function langs(): MorphToMany
    {
        return $this->morphToMany(PbLanguage::class, 'langable', 'langables', 'language_id', 'language_id');
    }
}
