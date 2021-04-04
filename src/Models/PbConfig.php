<?php

namespace Anibalealvarezs\Projectbuilder\Models;

use Anibalealvarezs\Projectbuilder\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PbConfig extends Model
{
    use ModelTrait;

    protected $table = 'config';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'configkey', 'configvalue', 'name', 'description'
    ];

    public static function findByKey($key)
    {
        $config = DB::table('config')
                ->select('id')
                ->where('configkey', $key)
                ->first();
        if ($config->id) {
            return self::find($config->id);
        }
        return null;
    }
}
