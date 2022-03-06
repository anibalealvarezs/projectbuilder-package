<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Interfaces\PbModelCrudInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PbFile extends PbBuilder implements PbModelCrudInterface
{
    protected $table = 'files';

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
        $this->publicRelations = ['author'];
        $this->allRelations = ['author'];
        $this->translatable = ['description', 'alt'];
        $this->appends = [...$this->appends, ...['descriptions', 'alts', 'url']];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'alt',
        'mime_type',
        'hash',
        'module',
        'permission',
        'user_id'
    ];

    public function getDescriptionAttribute($value)
    {
        return translateString($value);
    }

    public function getAltAttribute($value)
    {
        return translateString($value);
    }

    /**
     * The attributes that are mass assignable.
     *
     *
     * @return array|string
     * @var array
     */
    public function getAltsAttribute($value)
    {
        return $this->getRawOriginal('alt');
    }

    /**
     * The attributes that are mass assignable.
     *
     *
     * @return array|string
     * @var array
     */
    public function getUrlAttribute()
    {
        return config('app.url') . Storage::url($this->module.'/'.$this->name);
    }

    /**
     * The attributes that are mass assignable.
     *
     *
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(PbUser::class, 'user_id', 'id');
    }

    /**
     * The attributes that are mass assignable.
     *
     *
     * @param $id
     * @param $newId
     */
    public static function replaceAuthor($id, $newId): void
    {
        self::where('user_id', $id)->update([
            'user_id' => $newId
        ]);
    }

    /**
     * The attributes that are mass assignable.
     *
     *
     * @param bool $includeForm
     * @return array
     */
    public static function getCrudConfig(bool $includeForm = false): array
    {
        $config = parent::getCrudConfig();

        if ($includeForm) {
            $config['form'] = [
                'file' => [
                    'type' => 'file',
                ],
                'name' => [
                    'type' => 'text',
                ],
                'description' => [
                    'type' => 'text',
                ],
                'alt' => [
                    'type' => 'text',
                ],
                'permission' => [
                    'type' => 'select',
                    'list' => [
                        [
                            'id' => 'public',
                            'name' => 'Public'
                        ],
                        [
                            'id' => 'private',
                            'name' => 'Private'
                        ],
                    ],
                ],
                'module' => [
                    'type' => 'hidden',
                    'default' => 'file'
                ],
                'url' => [
                    'type' => 'hidden',
                ],
            ];
        }

        $config['fields'] = [
            'name' => [
                'href' => [
                    'route' => 'files.show',
                    'id' => true,
                ],
                'orderable' => true,
            ],
            'url' => [
                'href' => [
                    'custom' => 'self',
                    'text' => '#',
                    'target' => '_blank',
                ],
            ],
            'description' => [],
            'alt' => [],
            'mime_type' => [
                'name' => 'Mime Type',
                'orderable' => true,
            ],
            'module' => [],
            'permission' => [],
            'author' => [
                'arrval' => [
                    'key' => 'name',
                    'href' => [
                        'route' => 'users.show',
                        'id' => 'id',
                    ],
                ],
            ],
        ];

        $config['relations'] = ['author'];

        $config['custom_order'] = [
            'id', 'url', 'name', 'description', 'alt', 'mime_type', 'module', 'permission', 'author',
        ];

        $config['pagination'] = [
            'per_page' => 20,
            'location' => 'both',
        ];

        $config['heading'] = [
            'location' => 'both',
        ];

        return $config;
    }
}
