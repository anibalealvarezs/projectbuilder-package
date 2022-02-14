<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Interfaces\PbModelCrudInterface;
use Anibalealvarezs\Projectbuilder\Utilities\PbShares;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Auth;

class PbConfig extends PbBuilder implements PbModelCrudInterface
{
    protected $table = 'configs';

    public $timestamps = false;

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
        $this->publicRelations = ['module'];
        $this->allRelations = ['module'];
        $this->translatable = ['name', 'description'];
        $this->appends = [...$this->appends, ...['names', 'descriptions']];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'configkey', 'configvalue', 'name', 'description', 'module_id'
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

    public function getNameAttribute($value)
    {
        return translateString($value);
    }

    public function getDescriptionAttribute($value)
    {
        return translateString($value);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $key
     * @return PbConfig|null
     */
    public static function findByKey($key): self|null
    {
        return self::firstWhere('configkey', $key);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $key
     * @return mixed
     */
    public static function getValueByKey($key): mixed
    {
        if ($config = self::findByKey($key)) {
            return $config->configvalue;
        }
        return null;
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

        if ($includeForm) {
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
                'module' => [
                    'type' => 'select',
                    'list' => [
                        ...[['id' => 0, 'name' => '[none]']],
                        ...PbShares::getModules()['modules']->toArray()
                    ],
                ],
            ];
        }

        $config['relations'] = ['module'];

        $config['pagination'] = [
            'per_page' => 20,
            'location' => 'both',
        ];

        $config['heading'] = [
            'location' => 'both',
        ];

        $config['fields'] = [
            'name' => [],
            'configkey' => [
                'name' => 'Key',
                'orderable' => true,
            ],
            'configvalue' => [
                'name' => 'Value',
            ],
            'description' => [],
            'module' => [
                'arrval' => [
                    'key' => 'name',
                ],
            ],
        ];

        $config['custom_order'] = [
            'configkey', 'name', 'configvalue', 'description', 'module'
        ];

        return $config;
    }
}
