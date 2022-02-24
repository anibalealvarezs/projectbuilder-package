<?php

namespace Anibalealvarezs\Projectbuilder\Observers;

use Anibalealvarezs\Projectbuilder\Models\PbLogger;
use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Anibalealvarezs\Projectbuilder\Models\PbUser;

class PbUserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param PbUser $user
     * @return void
     */
    public function created(PbUser $user)
    {
        //
    }

    /**
     * Handle the User "updated" event.
     *
     * @param PbUser $user
     * @return void
     */
    public function updated(PbUser $user)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param PbUser $user
     * @return void
     */
    public function deleted(PbUser $user)
    {
        if (PbModule::exists('file') && class_exists(\Anibalealvarezs\Filemanager\Models\FmFile::class) && $default = PbUser::getDefaultUser()) {
            try {
                \Anibalealvarezs\Filemanager\Models\FmFile::replaceAuthor($user->id, $default->id);
            } catch (Exception $e) {
                PbLogger::create([
                    'severity' => 3,
                    'code' => 1,
                    'message' => 'Author not replaced. '.$e->getMessage(),
                    'object_type' => 'file',
                    'user_id' => Auth::user()->id,
                    'module_id' => PbModule::getByKey('file')?->id
                ]);
            }
        }
    }

    /**
     * Handle the User "forceDeleted" event.
     *
     * @param PbUser $user
     * @return void
     */
    public function forceDeleted(PbUser $user)
    {
        //
    }
}
