<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PbLogger extends PbBuilder
{
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
        $this->translatable = ['message'];
        $this->appends = [...$this->appends, ...['messages']];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'severity', 'code', 'message', 'object_type', 'object_id', 'user_id', 'module_id'
    ];

    /**
     * Scope a query to only include popular users.
     *
     * @return BelongsTo
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(PbModule::class, 'module_id', 'id');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(PbUser::class, 'user_id', 'id');
    }

    public function getMessageAttribute($value)
    {
        return PbHelpers::translateString($value);
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

    /**
     * Scope a query to only include popular users.
     *
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if (PbConfig::getValueByKey('_SAVE_LOGS_')) {
            parent::save();
        }
        return false;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function getEditableStatus(): bool
    {
        return false;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return array
     */
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
