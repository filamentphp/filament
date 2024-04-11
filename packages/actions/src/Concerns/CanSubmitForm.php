<?php

namespace Filament\Actions\Concerns;

trait CanSubmitForm
{
    protected bool $canSubmitForm = false;

    protected ?string $formToSubmit = null;

    protected ?string $formId = null;

    public function submit(?string $form): static
    {
        $this->canSubmitForm = filled($form);
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

    public function formId(?string $id): static
    {
        $this->formId = $id;

        return $this;
    }

    public function getFormId(): ?string
    {
        return $this->formId;
    }
}
