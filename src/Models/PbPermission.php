<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission;

class PbPermission extends Permission
{
    // use ModelTrait;

    protected $connection;

    function __construct() {
        $this->connection = config('database.default');
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
}
