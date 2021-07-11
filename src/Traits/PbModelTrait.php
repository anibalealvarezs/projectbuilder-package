<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

trait PbModelTrait {

    protected function getCrudAttribute(): array
    {
        return [
            'readable' => $this->getReadableStatus(),
            'editable' => $this->getEditableStatus(),
            'selectable' => $this->getSelectableStatus(),
            'deletable' => $this->getDeletableStatus(),
            'configurable' => $this->getConfigurableStatus(),
        ];
    }

    protected function getReadableStatus(): bool
    {
        return true;
    }

    protected function getEditableStatus(): bool
    {
        return true;
    }

    protected function getSelectableStatus(): bool
    {
        return true;
    }

    protected function getDeletableStatus(): bool
    {
        return true;
    }

    protected function getConfigurableStatus(): bool
    {
        return true;
    }
}
