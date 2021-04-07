<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class PbUser extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;
    use HasRoles;
    use ModelTrait;

    protected $guard_name = 'admin';

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'last_session', 'city_id', 'country_id', 'language_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /* public function delete()
    {
        // Remove langs relations
        $this->langs()->detach();

        // delete the user
        return parent::delete();
    } */

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(PbCountry::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(PbCity::class);
    }

    public function lang(): BelongsTo
    {
        return $this->belongsTo(PbLanguage::class, 'language_id', 'id');
    }

    /* public function langs(): MorphToMany
    {
        return $this->morphToMany(PbLanguage::class, 'langable', 'langables', 'language_id', 'language_id');
    } */

    public function getLocale(): string
    {
        $locale = PbLanguage::find($this->language_id);
        if ($locale) {
            return $locale->code;
        }
        return "";
    }
}
