<?php

namespace Anibalealvarezs\Projectbuilder\Helpers;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AeasHelpers
{
    public $vendor;
    public $package;
    public $dir;
    public $prefix;
    public $name;

    function __construct()
    {
        $this->vendor = 'Anibalealvarezs';
        $this->package = 'Projectbuilder';
        $this->dir = 'projectbuilder';
        $this->prefix = 'Pb';
        $this->name = 'builder';
    }

    public function setCollectionAttributeDatetimeFormat(
        $collection,
        $fields = [],
        $outputFormatType = 'method',
        $outputFormat = "toDateTimeString"
    ) {
        $collection = $collection->map(function ($array) use ($fields, $outputFormat, $outputFormatType) {
            foreach ($fields as $f) {
                $array[$f] = (
                $outputFormatType == "method" ?
                    Carbon::parse($array[$f])->{$outputFormat}() :
                    Carbon::parse($array[$f])->format($outputFormat)
                );
            }
            return $array;
        });
        return $collection;
    }

    public static function getCustomLocale()
    {
        if (Auth::check()) {
            $user = PbUser::find(Auth::user()->id);
            return $user->getLocale();
        }
        return "";
    }
}
