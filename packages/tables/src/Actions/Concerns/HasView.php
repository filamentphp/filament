<?php

namespace Filament\Tables\Actions\Concerns;

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
        $this->view('tables::actions.button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('tables::actions.link-action');

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('tables::actions.icon-button-action');

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }
}
