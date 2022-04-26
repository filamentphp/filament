<?php

namespace Filament\Support\Actions\Concerns;

use Illuminate\Contracts\View\View;

trait HasView
{
    protected string $view;

    public function view(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view($this->getView(), array_merge($this->data(), [
            'action' => $this,
        ]));
    }
}
