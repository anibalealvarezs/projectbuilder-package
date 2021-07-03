<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Traits\PbModelMiscTrait;
use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class PbUser extends User
{
    use Notifiable;
    use HasApiTokens;
    use HasRoles;
    use PbModelTrait;
    use PbModelMiscTrait;

    protected $guard_name = 'admin';

    protected $table = 'users';

    protected $appends = ['crud'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_session',
        'city_id',
        'country_id',
        'language_id'
    ];

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

    public function getLocale(): string
    {
        $locale = PbLanguage::find($this->language_id);
        if ($locale) {
            return $locale->code;
        }
        return "";
    }

    public function isEditableBy($id)
    {
        $user = self::find($id);
        if (!($this->hasRole('super-admin') && !$user->hasRole('super-admin')) &&
            !($this->hasRole(['admin']) && !$user->hasAnyRole(['super-admin', 'admin']))
        ) {
            return true;
        }

        return false;
    }

    public function isViewableBy($id)
    {
        $user = self::find($id);
        if (!($this->hasRole('super-admin') && !$user->hasRole('super-admin')) &&
            !($this->hasRole(['admin']) && !$user->hasAnyRole(['super-admin', 'admin']))
        ) {
            return true;
        }

        return false;
    }

    public function isSelectableBy($id)
    {
        return true;
    }

    public function isDeletableBy($id)
    {
        if (!$this->hasAnyRole(['super-admin', 'admin']) && ($this->id != Auth::user()->id)) {
            return true;
        }

        return false;
    }

    protected function getDeletableStatus()
    {
        $currentUser = self::find(Auth::user()->id);
        if ($this->id == $currentUser->id) {
            return false;
        }

        return true;
    }
}
