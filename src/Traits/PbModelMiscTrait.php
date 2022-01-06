<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

trait PbModelMiscTrait {

    /**
     * Remove the specified resource from storage.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
