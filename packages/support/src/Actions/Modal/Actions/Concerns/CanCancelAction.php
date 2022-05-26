<?php

namespace Filament\Support\Actions\Modal\Actions\Concerns;

trait CanCancelAction
{
    protected bool $canCancelAction = false;

    public function cancel(bool $condition = true): static
    {
        $this->canCancelAction = $condition;

        return $this;
    }

    public function canCancelAction(): bool
    {
        return $this->canCancelAction;
    }
}
