<?php

namespace Filament\Tables\Actions\Concerns;

trait CanOpenModal
{
    protected ?string $modalButtonLabel = null;

    protected ?string $modalHeading = null;

    protected ?string $modalSubheading = null;

    public function modalButton(?string $label = null): static
    {
        $this->modalButtonLabel = $label;

        return $this;
    }

    public function modalHeading(?string $heading = null): static
    {
        $this->modalHeading = $heading;

        return $this;
    }

    public function modalSubheading(?string $subheading = null): static
    {
        $this->modalSubheading = $subheading;

        return $this;
    }

    public function getModalButtonLabel(): string
    {
        if ($this->modalButtonLabel) {
            return $this->modalButtonLabel;
        }

        if ($this->isConfirmationRequired()) {
            return __('tables::table.actions.modal.buttons.confirm.label');
        }

        return __('tables::table.actions.modal.buttons.submit.label');
    }

    public function getModalHeading(): string
    {
        return $this->modalHeading ?? $this->getLabel();
    }

    public function getModalSubheading(): ?string
    {
        if ($this->modalSubheading) {
            return $this->modalSubheading;
        }

        if (! $this->isConfirmationRequired()) {
            return null;
        }

        return __('tables::table.actions.modal.requires_confirmation_subheading');
    }

    public function shouldOpenModal(): bool
    {
        if ($this->isConfirmationRequired()) {
            return true;
        }

        return $this->hasFormSchema();
    }
}
