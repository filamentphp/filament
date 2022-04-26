<?php

namespace Filament\Forms\Components\Actions\Modal\Actions\Concerns;

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
        $this->view('forms::components.actions.modal.actions.button-action');

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }
}
