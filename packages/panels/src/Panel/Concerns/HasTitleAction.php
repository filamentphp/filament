<?php

namespace Filament\Panel\Concerns;

trait HasTitleAction
{
    protected bool $hasTitleAction = true;

    public function titleAction(bool $showAction = true): static
    {
        $this->hasTitleAction = $showAction;

        return $this;
    }

    public function hasTitleAction(): bool
    {
        return $this->hasTitleAction;
    }
}
