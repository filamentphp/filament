<?php

namespace Filament\Pages\Actions\Concerns;

trait CanRequireConfirmation
{
    protected bool $isConfirmationRequired = false;

    public function requiresConfirmation(bool $condition = true): static
    {
        $this->isConfirmationRequired = $condition;

        return $this;
    }

    public function isConfirmationRequired(): bool
    {
        return $this->isConfirmationRequired;
    }
}
