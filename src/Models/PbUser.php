<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    protected $guard_name = 'admin';

    protected $table = 'users';

    protected $appends = ['api'];

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
        'language_id'
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
        return $query->find(Auth::user()->id);
    }

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
     * @param $id
     * @return bool
     */
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

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
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

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
    public function isSelectableBy($id): bool
    {
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $id
     * @return bool
     */
    public function isDeletableBy($id): bool
    {
        if (!$this->hasAnyRole(['super-admin', 'admin']) && ($this->id != Auth::user()->id)) {
            return true;
        }

        return false;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function getDeletableStatus(): bool
    {
        $currentUser = self::current();
        if ($this->id == $currentUser->id) {
            return false;
        }

        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function delete(): bool
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

    /**
     * Scope a query to only include popular users.
     *
     * @return PbUser|null
     */
    public function getDefaultUser(): self|null
    {
        return PbUser::find(1);
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

        $config['formconfig'] = [
            'name' => [
                'type' => 'text',
            ],
            'email' => [
                'type' => 'text',
            ],
            'country' => [
                'type' => 'select',
                'list' => Shares::getCountries()['countries']->toArray(),
            ],
            'lang' => [
                'type' => 'select',
                'list' => Shares::getLanguages()['languages']->toArray(),
            ],
            'roles' => [
                'type' => 'select-multiple',
                'list' => Shares::getRoles()['roles']->toArray(),
            ],
            'password' => [
                'type' => 'password',
            ],
        ];

        $config['fields'] = [
            'name' => [
                'href' => [
                    'route' => 'users.show',
                    'id' => true,
                ],
            ],
            'email' => [],
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
            ],
            'last_session' => [
                'name' => 'Last Session',
            ],
        ];

        $config['relations'] = ['country', 'city', 'lang', 'roles'];

        $config['custom_order'] = [
            'id', 'name', 'email', 'roles', 'country', 'lang', 'created_at', 'last_session'
        ];

        return $config;
    }
}
