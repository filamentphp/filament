<?php

namespace Filament\Actions\Concerns;

trait CanSubmitForm
{
    protected bool $canSubmitForm = false;

    protected ?string $formToSubmit = null;

    public function submit(?string $form = null): static
    {
        $this->canSubmitForm = true;
        $this->formToSubmit = $form;

        return $this;
    }

    public function canSubmitForm(): bool
    {
        return $this->canSubmitForm;
    }

    public function getFormToSubmit(): ?string
    {
        return $this->formToSubmit;
    }
}
