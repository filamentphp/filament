<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Contracts\View\View;

trait HasPreview
{
    protected string | Closure | null $preview = null;

    public function preview(string | Closure $preview): static
    {
        $this->preview = $preview;

        return $this;
    }

    public function renderPreview(): View
    {
        $this->view($this->evaluate($this->preview));

        return $this->render();
    }
}
