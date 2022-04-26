<?php

namespace Filament\Pages\Actions\Concerns;

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
        $this->view('filament::pages.actions.button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('filament::pages.actions.link-action');

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('filament::pages.actions.icon-button-action');

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }
}
