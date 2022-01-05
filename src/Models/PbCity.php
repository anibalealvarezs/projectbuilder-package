<?php

namespace Anibalealvarezs\Projectbuilder\Models;

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
        if ($json = json_decode($value)) {
            return $json->{app()->getLocale()};
        }
        return $value;
    }

    public function delete()
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

    public function country(): BelongsTo
    {
        return $this->belongsTo(PbCountry::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(PbUser::class);
    }

    public function langs(): MorphToMany
    {
        return $this->morphToMany(PbLanguage::class, 'langable', 'langables', 'language_id', 'language_id');
    }
}
