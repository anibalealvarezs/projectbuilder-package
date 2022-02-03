<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Traits\PbModelCrudTrait;
use Anibalealvarezs\Projectbuilder\Traits\PbModelTrait;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;
use Spatie\Translatable\HasTranslations;

class PbBuilder extends Model
{
    use PbModelTrait;
    use PbModelCrudTrait;
    use HasTranslations;

    protected $appends = ['crud'];

    public $translatable = [];

    /**
     * Scope a query to only include popular users.
     *
     * @return array
     */
    #[ArrayShape([
        'default' => "array",
        'relations' => "array",
        'action_routes' => "array",
        'enabled_actions' => "bool[]",
        'custom_order' => "array",
        'pagination' => "array",
        'heading' => "array",
    ])] public static function getCrudConfig(): array
    {
        return [
            'default' => [],
            'relations' => [],
            'action_routes' => [],
            'enabled_actions' => [
                'update' => true,
                'delete' => true,
                'create' => true,
            ],
            'custom_order' => [],
            'pagination' => [
                'location' => 'bottom',
            ],
            'heading' => [
                'location' => 'top',
            ],
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     *
     * @return array|string
     * @var array
     */
    public function getNamesAttribute()
    {
        return $this->getRawOriginal('name');
    }

    /**
     * The attributes that are mass assignable.
     *
     *
     * @return array|string
     * @var array
     */
    public function getAliasesAttribute($value)
    {
        return $this->getRawOriginal('alias');
    }

    /**
     * The attributes that are mass assignable.
     *
     *
     * @return array|string
     * @var array
     */
    public function getMessagesAttribute($value)
    {
        return $this->getRawOriginal('message');
    }

    /**
     * The attributes that are mass assignable.
     *
     *
     * @return array|string
     * @var array
     */
    public function getDescriptionsAttribute($value)
    {
        return $this->getRawOriginal('description');
    }
}
