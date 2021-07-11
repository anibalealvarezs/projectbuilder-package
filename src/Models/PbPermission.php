<?php

namespace Anibalealvarezs\Projectbuilder\Models;

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
    }

    public function getAliasAttribute($value)
    {
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
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
}
