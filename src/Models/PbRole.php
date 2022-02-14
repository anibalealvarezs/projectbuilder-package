<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Interfaces\PbModelCrudInterface;
use Anibalealvarezs\Projectbuilder\Utilities\PbShares;
use Anibalealvarezs\Projectbuilder\Traits\PbModelCrudTrait;
use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Translatable\HasTranslations;
use Auth;

class PbRole extends Role implements PbModelCrudInterface
{
    use HasTranslations;
    use PbModelTrait;
    use PbModelCrudTrait;

    protected $connection;

    public $translatable = ['alias'];

    protected $appends = ['crud', 'aliases'];

    function __construct() {
        $this->connection = config('database.default');
        $this->publicRelations = ['permissions'];
        $this->allRelations = ['permissions'];
        $this->unreadableModels = [
            'name' => [
                'super-admin',
            ]
        ];
        $this->undeletableModels = [
            'name' => [
                'user',
                'api-user',
                'admin',
                'developer',
                'super-admin',
            ]
        ];
        $this->unmodifiableModels = [
            'name' => [
                'user',
                'api-user',
                'admin',
                'developer',
                'super-admin',
            ]
        ];
    }

    public function getAliasAttribute($value)
    {
        return translateString($value);
    }

    public function getAliasesAttribute($value)
    {
        return $this->getRawOriginal('alias');
    }

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            PbPermission::class,
            config('permission.table_names.role_has_permissions'),
            PermissionRegistrar::$pivotRole,
            PermissionRegistrar::$pivotPermission
        );
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
        $config = PbBuilder::getCrudConfig();

        $config['fields'] = [
            'name' => [
                'orderable' => true,
            ],
            'alias' => [],
            'permissions' => [
                'arrval' => [
                    'key' => 'alias',
                    'href' => [
                        'route' => 'permissions.show',
                        'id' => 'id',
                    ],
                ],
                'size' => 'multiple',
            ],
        ];

        $config['pagination'] = [
            'per_page' => 10,
            'location' => 'both',
        ];

        if ($includeForm) {
            $config['formconfig'] = [
                'name' => [
                    'type' => 'text',
                ],
                'alias' => [
                    'type' => 'text',
                ],
                'permissions' => [
                    'type' => 'select-multiple',
                    'list' => PbShares::getPermissionsAll()['permissionsall']->toArray(),
                ],
            ];
        }

        $config['relations'] = ['permissions'];

        return $config;
    }
}
