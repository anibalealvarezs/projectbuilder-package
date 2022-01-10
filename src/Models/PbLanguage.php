<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Builder;

class PbLanguage extends PbBuilder
{
    use HasTranslations;

    protected $table = 'languages';

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
        $this->publicRelations = ['cities', 'countries'];
        $this->allRelations = ['users', 'cities', 'countries'];
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
    public function delete(): bool
    {
        // Remove language from users
        $this->updateUserLanguage();

        // delete the user
        return parent::delete();
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function enable(): bool
    {
        // Remove language from users
        $this->updateUserLanguage();

        $this->status = true;
        return $this->update();
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @return Builder
     */
    public static function scopeEnabled(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function updateUserLanguage(): bool
    {
        return PbUser::where('language_id', $this->id)->update(['language_id' => null]);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function disable(): bool
    {
        $this->status = false;
        return $this->update();
    }

    public function getNameAttribute($value)
    {
        return PbHelpers::translateString($value);
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
