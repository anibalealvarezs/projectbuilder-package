<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Interfaces\PbModelCrudInterface;
use Anibalealvarezs\Projectbuilder\Utilities\PbShares;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Auth;

class PbLogger extends PbBuilder implements PbModelCrudInterface
{
    protected $table = 'loggers';

    protected $appends = ['crud'];

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
        return translateString($value);
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
     * @param $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/y H:i:s');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if (getConfigValue('_SAVE_LOGS_')) {
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
     * @param $id
     * @return bool|PbUser|PbCurrentUser
     */
    public function getAuthorizedUser($id): bool|PbUser|PbCurrentUser
    {
        return (Auth::user()->id === $id ? app(PbCurrentUser::class) : PbUser::find($id));
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param bool $includeForm
     * @return array
     */
    public static function getCrudConfig(bool $includeForm = false): array
    {
        $config = parent::getCrudConfig();

        $config['relations'] = ['user', 'module'];

        $config['pagination'] = [
            'per_page' => 50,
            'location' => 'both',
        ];

        $config['heading'] = [
            'location' => 'both',
        ];

        if ($includeForm) {
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
                'module' => [
                    'type' => 'select',
                    'list' => [
                        ...[['id' => 0, 'name' => '[none]']],
                        ...PbShares::getModules()['modules']->toArray()
                    ],
                ],
            ];
        }

        $config['fields'] = [
            'severity' => [
                'orderable' => true,
            ],
            'code' => [
                'orderable' => true,
            ],
            'message' => [],
            'object_type' => [
                'name' => 'Object Type',
                'orderable' => true,
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
                'orderable' => true,
            ],
        ];

        return $config;
    }
}
