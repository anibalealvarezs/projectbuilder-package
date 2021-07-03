<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Traits\PbModelMiscTrait;
use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;

class PbLanguage extends Model
{
    use PbModelTrait;
    use PbModelMiscTrait;
    use HasTranslations;

    protected $table = 'languages';

    public $translatable = ['name'];

    protected $appends = ['crud'];

    public $timestamps = false;

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
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
        }
        return $value;
    }

    public static function findByCode($code)
    {
        $lang = DB::table('languages')
                ->select('id')
                ->where('code', $code)
                ->first();
        if ($lang->id) {
            return self::find($lang->id);
        }
        return null;
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

    public function isEditableBy($id): bool
    {
        return true;
    }

    public function isViewableBy($id): bool
    {
        return true;
    }

    public function isSelectableBy($id): bool
    {
        return true;
    }

    public function isDeletableBy($id): bool
    {
        return true;
    }
}
