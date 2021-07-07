<?php

namespace Anibalealvarezs\Projectbuilder\Helpers;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PbHelpers
{
    public const PB_VENDOR = 'Anibalealvarezs';
    public const PB_PACKAGE = 'Projectbuilder';
    public const PB_DIR = 'projectbuilder';
    public const PB_PREFIX = 'Pb';
    public const PB_NAME = 'builder';

    function __construct()
    {
        //
    }

    public static function setCollectionAttributeDatetimeFormat(
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

    public static function toPlural($string): string
    {
        return $string.'s';
    }
}
