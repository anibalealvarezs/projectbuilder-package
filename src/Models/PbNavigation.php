<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PbNavigation extends PbBuilder
{
    use HasTranslations;

    protected $table = 'navigations';

    public $translatable = ['name'];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'destiny', 'type', 'parent', 'permission_id', 'module_id', 'position', 'status'
    ];

    public function getNameAttribute($value)
    {
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
        }
        return $value;
    }

    public function ascendant(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent', 'id')->with('ascendant');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(PbPermission::class);
    }

    public function descendants(): HasMany
    {
        // Recursive Relationship
        return $this->hasMany(self::class, 'parent', 'id')->orderBy('position')->with('descendants');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(PbModule::class, 'module_id', 'id');
    }

    public static function getCrudConfig(): array
    {
        $config = parent::getCrudConfig();

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
                'list' => array_merge([['id'=>0, 'name'=>'[none]']], Shares::getNavigations()['navigations']['full']->toArray()),
            ],
            'permission' => [
                'type' => 'select',
                'list' => Shares::getPermissionsAll()['permissionsall']->toArray(),
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
                'type' => 'text',
            ],
        ];

        $config['relations'] = ['ascendant', 'permission', 'module'];

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
            'status' => [
                'style' => [
                    'centered' => true,
                    'width' => 'w-12',
                ],
                'status' => true
            ],
            'module' => [
                'arrval' => [
                    'key' => 'alias',
                ],
            ],
        ];

        return $config;
    }
}
