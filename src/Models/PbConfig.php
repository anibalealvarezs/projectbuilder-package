<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        return self::firstWhere('configkey', $key);
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
        $config = parent::getCrudConfig();

        $config['formconfig'] = [
            'name' => [
                'type' => 'text',
            ],
            'configkey' => [
                'type' => 'text',
            ],
            'configvalue' => [
                'type' => 'text',
            ],
            'description' => [
                'type' => 'textarea',
            ],
        ];

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
