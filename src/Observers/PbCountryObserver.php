<?php

namespace Anibalealvarezs\Projectbuilder\Observers;

use Anibalealvarezs\Projectbuilder\Models\PbCity;
use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbUser;

class PbCountryObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param PbCountry $country
     * @return void
     */
    public function created(PbCountry $country)
    {
        //
    }

    /**
     * Handle the User "updated" event.
     *
     * @param PbCountry $country
     * @return void
     */
    public function updated(PbCountry $country)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param PbCountry $country
     * @return void
     */
    public function deleted(PbCountry $country)
    {
        DB::transaction(function() use ($country) {

            // Remove langs relations
            $country->langs()->detach();

            // Delete cities
            PbCity::where('country_id', $country->id)->delete();

            // Remove countries from users
            PbUser::removeCountry($country);
        });
    }

    /**
     * Handle the User "forceDeleted" event.
     *
     * @param PbCountry $country
     * @return void
     */
    public function forceDeleted(PbCountry $country)
    {
        //
    }
}
