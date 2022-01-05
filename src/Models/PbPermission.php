<?php

namespace Anibalealvarezs\Projectbuilder\Models;

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

    protected $appends = ['crud'];

    function __construct() {
        $this->connection = config('database.default');
        $this->publicRelations = ['roles', 'module'];
        $this->allRelations = ['roles', 'module'];
    }

    public function getAliasAttribute($value)
    {
        if ($json = json_decode($value)) {
            return $json->{app()->getLocale()};
        }
        return $value;
    }

    public function delete()
    {
        // Remove countries from users
        PbNavigation::where('permission_id', $this->id)->update(['permission_id' => null]);

        // delete the country
        return parent::delete();
    }

    public function navigations(): HasMany
    {
        // Recursive Relationship
        return $this->hasMany(PbNavigation::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(PbModule::class, 'module_id', 'id');
    }

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
                    'key' => 'alias',
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
