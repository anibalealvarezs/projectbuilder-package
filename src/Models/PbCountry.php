<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;


class PbCountry extends PbBuilder
{
    use HasTranslations;

    protected $table = 'countries';

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
        $this->publicRelations = ['cities', 'capital', 'langs'];
        $this->allRelations = ['users', 'cities', 'capital', 'langs'];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'capital_id'
    ];

    public function getNameAttribute($value)
    {
        return PbHelpers::translateString($value);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $code
     * @return PbCountry|null
     */
    public static function findByCode($code): self|null
    {
        if ($country = DB::table('countries')
            ->select('id')
            ->where('code', $code)
            ->first()) {
            return self::find($country->id);
        }
        return null;
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

        // Delete cities
        PbCity::where('country_id', $this->id)->delete();

        // Remove countries from users
        PbUser::where('country_id', $this->id)->update(['country_id' => null]);

        // delete the country
        return parent::delete();
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return HasMany
     */
    public function cities(): HasMany
    {
        return $this->hasMany(PbCity::class);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return HasOne
     */
    public function capital(): HasOne
    {
        return $this->hasOne(PbCity::class);
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
