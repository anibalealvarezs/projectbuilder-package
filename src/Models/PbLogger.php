<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class PbLogger extends PbBuilder
{
    use HasTranslations;

    public $translatable = ['message'];

    protected $table = 'logger';

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->publicRelations = ['user', 'module'];
        $this->allRelations = ['user', 'module'];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'severity', 'code', 'message', 'object_type', 'object_id', 'user_id', 'module_id'
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(PbModule::class, 'module_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(PbUser::class, 'user_id', 'id');
    }

    public function getMessageAttribute($value)
    {
        if ($json = json_decode($value)) {
            return $json->{app()->getLocale()};
        }
        return $value;
    }

    public function getSeverityAttribute($value)
    {
        return match ($value) {
            1 => 'Info',
            2 => 'Warning',
            3 => 'Error',
            default => 'Debug',
        };
    }

    public function save(array $options = [])
    {
        if (PbConfig::getValueByKey('_SAVE_LOGS_')) {
            parent::save();
        }
        return false;
    }

    protected function getEditableStatus(): bool
    {
        return false;
    }

    public static function getCrudConfig(): array
    {
        $config = parent::getCrudConfig();

        $config['relations'] = ['user', 'module'];

        $config['formconfig'] = [
            'name' => [
                'type' => 'text',
            ],
            'loggerkey' => [
                'type' => 'text',
            ],
            'loggervalue' => [
                'type' => 'text',
            ],
            'description' => [
                'type' => 'textarea',
            ],
        ];

        $config['fields'] = [
            'severity' => [],
            'code' => [],
            'message' => [],
            'object_type' => [
                'name' => 'Object Type',
            ],
            'object_id' => [
                'name' => 'Object ID',
            ],
            'user' => [
                'arrval' => [
                    'key' => 'name',
                    'href' => [
                        'route' => 'users.show',
                        'id' => 'id',
                    ],
                ],
            ],
            'module' => [
                'arrval' => [
                    'key' => 'name',
                ],
            ],
            'created_at' => [
                'name' => 'Created At',
            ],
        ];

        return $config;
    }
}
