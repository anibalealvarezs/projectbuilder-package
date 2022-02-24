<?php

namespace Anibalealvarezs\Projectbuilder\Observers;

use Anibalealvarezs\Projectbuilder\Models\PbCity;
use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbUser;

class PbCityObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param PbCity $city
     * @return void
     */
    public function created(PbCity $city)
    {
        //
    }

    /**
     * Handle the User "updated" event.
     *
     * @param PbCity $city
     * @return void
     */
    public function updated(PbCity $city)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param PbCity $city
     * @return void
     */
    public function deleted(PbCity $city)
    {
        DB::transaction(function() use ($city) {

            // Remove langs relations
            $city->langs()->detach();

            // Remove cities from users
            PbUser::removeCity($city);

            // Remove cities from users
            PbCountry::removeCapital($city);
        });
    }

    /**
     * Handle the User "forceDeleted" event.
     *
     * @param PbCity $city
     * @return void
     */
    public function forceDeleted(PbCity $city)
    {
        //
    }
}
