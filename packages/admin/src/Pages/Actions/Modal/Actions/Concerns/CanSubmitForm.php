<?php

namespace Filament\Pages\Actions\Modal\Actions\Concerns;

trait CanSubmitForm
{
    protected bool $canSubmitForm = false;

    protected ?string $form = null;

    public function submit(?string $form = null): static
    {
        $this->canSubmitForm = true;
        $this->form = $form;

        return $this;
    }

    public function canSubmitForm(): bool
    {
        return $this->canSubmitForm;
    }

    public function getForm(): ?string
    {
        return $this->form;
    }
}
