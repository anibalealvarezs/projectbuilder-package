<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Interfaces\PbModelCrudInterface;
use Anibalealvarezs\Projectbuilder\Utilities\PbShares;
use Anibalealvarezs\Projectbuilder\Traits\PbModelEnableableTrait;
use Anibalealvarezs\Projectbuilder\Traits\PbModelSortableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class PbNavigation extends PbBuilder implements PbModelCrudInterface
{
    use PbModelEnableableTrait;
    use PbModelSortableTrait;

    protected $table = 'navigations';

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
        $this->publicRelations = ['ascendant', 'permission', 'module'];
        $this->allRelations = ['ascendant', 'permission', 'module'];
        $this->translatable = ['name'];
        $this->appends = [...$this->appends, ...['names']];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'destiny', 'type', 'parent', 'permission_id', 'module_id', 'position', 'status'
    ];

    /**
     * The attributes that are mass assignable.
     *
     *
     * @return array|string
     * @var array
     */
    public function getNameAttribute($value)
    {
        return translateString($value);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return BelongsTo
     */
    public function ascendant(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent', 'id')->with('ascendant');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return BelongsTo
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(PbPermission::class);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return HasMany
     */
    public function descendants(): HasMany
    {
        // Recursive Relationship
        return $this->hasMany(self::class, 'parent', 'id')->orderBy('position')->with('descendants');
    }

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
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrderedByDefault(Builder $query): Builder
    {
        return $query->orderBy('parent')->orderBy('position');
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
                'destiny' => [
                    'type' => 'textarea',
                ],
                'type' => [
                    'type' => 'select',
                    'list' => [
                        [
                            'id' => 'route',
                            'name' => 'Route'
                        ],
                        [
                            'id' => 'path',
                            'name' => 'Path'
                        ],
                        [
                            'id' => 'custom',
                            'name' => 'Custom'
                        ],
                    ],
                ],
                'parent' => [
                    'type' => 'select',
                    'list' => [
                        ...[['id'=>0, 'name'=>'[none]']],
                        ...PbShares::getNavigations()['navigations']['full']->toArray()
                    ],
                ],
                'permission' => [
                    'type' => 'select',
                    'list' => PbShares::getPermissionsAll()['permissionsall']->toArray(),
                ],
                'status' => [
                    'type' => 'select',
                    'list' => [
                        [
                            'id' => '1',
                            'name' => 'Enabled'
                        ],
                        [
                            'id' => '0',
                            'name' => 'Disabled'
                        ],
                    ],
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

        $config['relations'] = ['ascendant', 'permission', 'module'];

        $config['pagination'] = [
            'per_page' => 20,
            'location' => 'both',
        ];

        $config['heading'] = [
            'location' => 'both',
        ];

        $config['fields'] = [
            'name' => [],
            'destiny' => [],
            'type' => [],
            'ascendant' => [
                'name' => 'Parent',
                'arrval' => [
                    'key' => 'name',
                ],
            ],
            'permission' => [
                'arrval' => [
                    'key' => 'alias',
                ],
            ],
            'module' => [
                'arrval' => [
                    'key' => 'name',
                ],
            ],
        ];

        if ((new self)->isEditableBy(Auth::user()->id)) {
            $config['fields']['status'] = [
                'style' => [
                    'centered' => true,
                    'width' => 'w-12',
                ],
                'status' => true
            ];
        }

        return $config;
    }
}
