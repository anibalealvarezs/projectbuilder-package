<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Translatable\HasTranslations;

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

    public function delete()
    {
        // Remove language from users
        $this->updateUserLanguage();

        // delete the user
        return parent::delete();
    }

    public function enable()
    {
        // Remove language from users
        $this->updateUserLanguage();

        $this->status = true;
        $this->update();
    }

    public static function getEnabled()
    {
        return self::where('status', true);
    }

    protected function updateUserLanguage()
    {
        PbUser::where('language_id', $this->id)->update(['language_id' => null]);
    }

    public function disable()
    {
        $this->status = false;
        $this->update();
    }

    public function getNameAttribute($value)
    {
        if ($json = json_decode($value)) {
            return $json->{app()->getLocale()};
        }
        return $value;
    }

    public static function findByCode($code)
    {
        return self::firstWhere('code', $code);
    }

    public function cities(): MorphToMany
    {
        return $this->morphedByMany(PbCity::class, 'langable');
    }

    public function countries(): MorphToMany
    {
        return $this->morphedByMany(PbCountry::class, 'langable');
    }

    public function users(): HasMany
    {
        return $this->hasMany(PbUser::class);
    }
}
