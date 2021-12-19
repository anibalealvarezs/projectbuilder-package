<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;

class PbConfig extends PbBuilder
{
    use HasTranslations;

    protected $table = 'config';

    public $translatable = ['name', 'description'];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'configkey', 'configvalue', 'name', 'description', 'module_id'
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(PbModule::class, 'module_id', 'id');
    }

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
        if ($config) {
            return self::find($config->id);
        }
        return null;
    }

    public static function getValueByKey($key)
    {
        if ($config = self::findByKey($key)) {
            return $config->configvalue;
        }
        return null;
    }

    public static function getCrudConfig(): array
    {
        $config = PbBuilder::getCrudConfig();

        $config['relations'] = ['module'];

        $config['fields'] = [
            'name' => [],
            'configkey' => [
                'name' => 'Key',
            ],
            'configvalue' => [
                'name' => 'Value',
            ],
            'description' => [],
        ];

        return $config;
    }
}
