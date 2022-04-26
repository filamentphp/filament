<?php

namespace Filament\Pages\Actions\Modal\Actions\Concerns;

trait HasView
{
    protected string $view;

    public function view(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function button(): static
    {
        $this->view('filament::pages.actions.modal.actions.button-action');

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }
}
