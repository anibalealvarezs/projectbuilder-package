<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Traits\PbModelMiscTrait;
use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;

class PbConfig extends Model
{
    use PbModelTrait;
    use PbModelMiscTrait;
    use HasTranslations;

    protected $table = 'config';

    public $translatable = ['name', 'description'];

    protected $appends = ['crud'];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'configkey', 'configvalue', 'name', 'description'
    ];

    public function getNameAttribute($value)
    {
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
        }
        return $value;
    }

    public function getDescriptionAttribute($value)
    {
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
        }
        return $value;
    }

    public static function findByKey($key)
    {
        $config = DB::table('config')
                ->select('id')
                ->where('configkey', $key)
                ->first();
        if ($config->id) {
            return self::find($config->id);
        }
        return null;
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
