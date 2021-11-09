<?php

namespace Filament\View\Components\Actions\Concerns;

trait CanSubmitForm
{
    protected bool $canSubmitForm = false;

    public function submit(bool $condition = true): static
    {
        $this->canSubmitForm = $condition;

        return $this;
    }

    public function canSubmitForm(): bool
    {
        return $this->canSubmitForm;
    }
}
