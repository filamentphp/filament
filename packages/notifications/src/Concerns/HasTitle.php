<?php

namespace Filament\Notifications\Concerns;

use Closure;

trait HasTitle
{
    protected string | Closure | null $title = null;

    protected bool $shouldTranslateTitle = false;

    public function title(string | Closure | null $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function translateTitle(bool $shouldTranslateTitle = true): static
    {
        $this->shouldTranslateTitle = $shouldTranslateTitle;

        return $this;
    }

    public function getTitle(): ?string
    {
        $title = $this->evaluate($this->title);

        return is_string($title) && $this->shouldTranslateTitle
            ? __($title)
            : $title;
    }
}
