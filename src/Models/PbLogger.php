<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class PbLogger extends PbBuilder
{
    use HasRoles;
    use HasTranslations;

    public $translatable = ['message'];

    protected $table = 'logger';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'severity', 'code', 'message', 'object_type', 'object_id', 'user_id', 'module_id'
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(PbModule::class, 'module_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(PbUser::class, 'user_id', 'id');
    }

    public function getMessageAttribute($value)
    {
        if (json_decode($value)) {
            return json_decode($value)->{app()->getLocale()};
        }
        return $value;
    }
}
