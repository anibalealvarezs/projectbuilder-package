<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Traits\PbModelCrudTrait;
use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission;
use Spatie\Translatable\HasTranslations;

class PbPermission extends Permission
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
    }

    public function getAliasAttribute($value)
    {
        return PbHelpers::translateString($value);
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
        // Remove countries from users
        PbNavigation::where('permission_id', $this->id)->update(['permission_id' => null]);

        // delete the country
        return parent::delete();
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
     * Scope a query to only include popular users.
     *
     * @return array
     */
    public static function getCrudConfig(): array
    {
        $config = PbBuilder::getCrudConfig();

        $config['action_routes'] = [
            'update' => [
                'key' => 'id',
                'altroute' => "profile.show"
            ],
        ];

        $config['fields'] = [
            'name' => [],
            'alias' => [],
            'module' => [
                'arrval' => [
                    'key' => 'name',
                ],
            ],
        ];

        $config['formconfig'] = [
            'name' => [
                'type' => 'text',
            ],
            'alias' => [
                'type' => 'text',
            ],
            'roles' => [
                'type' => 'select-multiple',
                'list' => Shares::getRoles()['roles']->toArray(),
            ],
        ];

        $config['relations'] = ['roles', 'module'];

        return $config;
    }
}
