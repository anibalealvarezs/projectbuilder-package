<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Interfaces\PbModelCrudInterface;
use Anibalealvarezs\Projectbuilder\Utilities\PbShares;
use Anibalealvarezs\Projectbuilder\Traits\PbModelCrudTrait;
use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Translatable\HasTranslations;
use Auth;

class PbPermission extends Permission implements PbModelCrudInterface
{
    use HasTranslations;
    use PbModelTrait;
    use PbModelCrudTrait;

    protected $connection;

    public $translatable = ['alias'];

    protected $appends = ['crud', 'aliases'];

    function __construct() {
        $this->connection = config('database.default');
        $this->publicRelations = ['roles', 'module'];
        $this->allRelations = ['roles', 'module'];
        $this->unmodifiableModels = [
            'name' => [
                'crud super-admin',
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
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function delete(): bool
    {
        return DB::transaction(function() {

            // Remove countries from users
            PbNavigation::where('permission_id', $this->id)->update(['permission_id' => null]);

            // delete the country
            return parent::delete();
        });
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return HasMany
     */
    public function navigations(): HasMany
    {
        // Recursive Relationship
        return $this->hasMany(PbNavigation::class);
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
     * A permission can be applied to roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            PbRole::class,
            config('permission.table_names.role_has_permissions'),
            PermissionRegistrar::$pivotPermission,
            PermissionRegistrar::$pivotRole
        );
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $key
     * @return PbPermission|null
     */
    public static function findByNameCustom($key): self|null
    {
        return self::firstWhere('name', $key);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
    public function isDeletableBy($id): bool
    {
        return false; // Undeletable till we have a better way to handle permission deletion
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

        $config['action_routes'] = [
            'update' => [
                'key' => 'id',
                'altroute' => "profile.show"
            ],
        ];

        $config['fields'] = [
            'name' => [
                'orderable' => true,
            ],
            'alias' => [],
            'roles' => [
                'arrval' => [
                    'key' => 'alias',
                    'href' => [
                        'route' => 'roles.show',
                        'id' => 'id',
                    ],
                ],
                'size' => 'multiple',
            ],
            'module' => [
                'arrval' => [
                    'key' => 'name',
                ],
            ],
        ];

        $config['pagination'] = [
            'per_page' => 10,
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
                'alias' => [
                    'type' => 'text',
                ],
                'roles' => [
                    'type' => 'select-multiple',
                    'list' => PbShares::getRoles()['roles']->toArray(),
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

        $config['relations'] = ['roles', 'module'];

        return $config;
    }
}
