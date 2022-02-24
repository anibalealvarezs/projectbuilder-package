<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Interfaces\PbModelCrudInterface;
use Anibalealvarezs\Projectbuilder\Traits\PbModelCrudTrait;
use Anibalealvarezs\Projectbuilder\Utilities\PbShares;
use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class PbUser extends User implements PbModelCrudInterface
{
    use Notifiable;
    use HasApiTokens;
    use HasRoles;
    use PbModelTrait;
    use PbModelCrudTrait;
    use HasTranslations;

    protected $guard_name = 'admin';

    protected $table = 'users';

    protected $appends = ['api', 'crud'];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

    public $translatable = [];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->publicRelations = ['country', 'city', 'lang', 'roles'];
        $this->allRelations = ['country', 'city', 'lang', 'roles'];
    }

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
        'language_id',
        'profile_photo_path',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(PbCountry::class);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(PbCity::class);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/y');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return string
     */
    public function scopeWithAllPermissions()
    {
        $this->permissions = $this->getAllPermissions();
        return $this;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return BelongsTo
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(PbLanguage::class, 'language_id', 'id');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->morphToMany(
            PbRole::class,
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            'role_id'
        );
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @return PbUser|null
     */
    public function scopeCurrent(Builder $query): self|null
    {
        if ($user = $query->find(Auth::user()->id)->withAllPermissions()) {
            $user->roles = $user->roles()->get();
            return $user;
        }
        return null;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRemoveAdmins(Builder $query): Builder
    {
        $currentUser = app(PbCurrentUser::class);
        if (!$currentUser->hasRole('super-admin')) {
            $superAdmins = self::role('super-admin')->get()->modelKeys();
            $query->whereNotIn('id', $superAdmins);
            if (!$currentUser->hasRole('admin')) {
                $admins = self::role('admin')->get()->modelKeys();
                $query->whereNotIn('id', $admins);
            }
        }
        return $query;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @param $language
     * @return bool
     */
    public function scopeRemoveLanguage(Builder $query, $language): bool
    {
        return $query->where('language_id', $language->id)->update(['language_id' => null]);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @param $country
     * @return bool
     */
    public function scopeRemoveCountry(Builder $query, $country): bool
    {
        return $query->where('country_id', $country->id)->update(['country_id' => null]);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Builder $query
     * @param $city
     * @return bool
     */
    public function scopeRemoveCity(Builder $query, $city): bool
    {
        return $query->where('city_id', $city->id)->update(['city_id' => null]);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function getApiAttribute(): bool
    {
        return $this->hasPermissionTo('api access');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return string
     */
    public function getLocale(): string
    {
        if ($locale = PbLanguage::find($this->language_id)) {
            return $locale->code;
        }
        return "";
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return string
     */
    public function scopeGetMyLocale(): string
    {
        return $this->current()->getLocale();
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
    public function isDeletableBy($id): bool
    {
        if (!$user = $this->getAuthorizedUser($id)) {
            return false;
        }

        if ($this->id === $id) {
            return false;
        }

        if ($this->hasRole('super-admin')) {
            return false;
        }

        if ($this->hasRole(['admin']) && !$user->hasRole('super-admin')){
            return false;
        }

        if (!$user->hasPermissionTo('delete '.$this->getTable())) {
            return false;
        }

        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool|PbUser|PbCurrentUser
     */
    public function getAuthorizedUser($id): bool|PbUser|PbCurrentUser
    {
        if (Auth::user()->id === $id) {
            $user = app(PbCurrentUser::class);
        }

        if (!isset($user) || !$user) {
            $user = self::find($id);
        }

        if (!$user) {
            return false;
        }

        if ($this->hasRole('super-admin') && !$user->hasRole('super-admin')) {
            return false;
        }

        if ($this->hasRole('admin') && !$user->hasAnyRole(['super-admin', 'admin'])) {
            return false;
        }

        return $user;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param bool $only_names
     * @return array|Collection
     */
    public function scopeCurrentPermissions($only_names = false): array|Collection
    {
        if (!$only_names) {
            return $this->permissions;
        }
        return $this->permissions->pluck('name')->toArray();
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param bool $only_names
     * @return array|Collection
     */
    public function scopeCurrentRoles($only_names = false): array|Collection
    {
        if (!$only_names) {
            return $this->roles;
        }
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return PbUser|null
     */
    public static function getDefaultUser(): self|null
    {
        return PbUser::first();
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

        if ($includeForm) {
            $config['form'] = [
                'name' => [
                    'type' => 'text',
                ],
                'email' => [
                    'type' => 'text',
                ],
                'country' => [
                    'type' => 'select',
                    'list' => PbShares::getCountries()['countries']->toArray(),
                ],
                'lang' => [
                    'type' => 'select',
                    'list' => PbShares::getLanguages()['languages']->toArray(),
                ],
                'roles' => [
                    'type' => 'select-multiple',
                    'list' => PbShares::getRoles()['roles']->toArray(),
                ],
                'password' => [
                    'type' => 'password',
                ],
            ];
        }

        $config['fields'] = [
            'name' => [
                'href' => [
                    'route' => 'users.show',
                    'id' => true,
                ],
                'orderable' => true,
            ],
            'email' => [
                'orderable' => true,
            ],
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
            'country' => [
                'arrval' => [
                    'key' => 'name',
                ],
            ],
            'lang' => [
                'name' => 'Language',
                'arrval' => [
                    'key' => 'name',
                ],
            ],
            'created_at' => [
                'name' => 'Created At',
                'orderable' => true,
            ],
        ];

        $config['relations'] = ['country', 'city', 'lang', 'roles'];

        $config['custom_order'] = [
            'id', 'name', 'email', 'roles', 'country', 'lang', 'created_at'
        ];

        return $config;
    }
}
