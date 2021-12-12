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

    public function isEditableBy($id): bool
    {
        $user = self::find($id);
        if (!($this->hasRole('super-admin') && !$user->hasRole('super-admin')) &&
            !($this->hasRole(['admin']) && !$user->hasAnyRole(['super-admin', 'admin']))
        ) {
            return true;
        }

        return false;
    }

    public function isViewableBy($id): bool
    {
        $user = self::find($id);
        if (!($this->hasRole('super-admin') && !$user->hasRole('super-admin')) &&
            !($this->hasRole(['admin']) && !$user->hasAnyRole(['super-admin', 'admin']))
        ) {
            return true;
        }

        return false;
    }

    public function isSelectableBy($id): bool
    {
        return true;
    }

    public function isDeletableBy($id): bool
    {
        if (!$this->hasAnyRole(['super-admin', 'admin']) && ($this->id != Auth::user()->id)) {
            return true;
        }

        return false;
    }

    protected function getDeletableStatus(): bool
    {
        $currentUser = self::find(Auth::user()->id);
        if ($this->id == $currentUser->id) {
            return false;
        }

        return true;
    }

    public function delete()
    {
        if (PbModule::exists('filemanager') && class_exists(\Anibalealvarezs\Filemanager\Models\FmFile::class)) {
            $user = self::getDefaultUser();
            if ($user) {
                try {
                    \Anibalealvarezs\Filemanager\Models\FmFile::replaceAuthor($this->id, $user->id);
                } catch (Exception $e) {
                    $module = PbModule::getByKey('filemanager');
                    PbLogger::create([
                        'severity' => 3,
                        'code' => 1,
                        'message' => 'Author not replaced. '.$e->getMessage(),
                        'object_type' => 'file',
                        'user_id' => Auth::user()->id,
                        'module_id' => ($module ? $module->id : null)
                    ]);
                }
            }
        }

        // delete the country
        return parent::delete();
    }

    public function getDefaultUser()
    {
        return PbUser::find(1);
    }
}
