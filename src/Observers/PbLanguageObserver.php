<?php

namespace Anibalealvarezs\Projectbuilder\Observers;

use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Models\PbUser;

class PbLanguageObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param PbLanguage $language
     * @return void
     */
    public function created(PbLanguage $language)
    {
        //
    }

    /**
     * Handle the User "updated" event.
     *
     * @param PbLanguage $language
     * @return void
     */
    public function updated(PbLanguage $language)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param PbLanguage $language
     * @return void
     */
    public function deleted(PbLanguage $language)
    {
        PbUser::removeLanguage($language);
    }

    /**
     * Handle the User "forceDeleted" event.
     *
     * @param PbLanguage $language
     * @return void
     */
    public function forceDeleted(PbLanguage $language)
    {
        //
    }
}
