<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Illuminate\Support\HtmlString;

trait HasHint
{
    protected string | HtmlString | Closure | null $hint = null;

    protected string | Closure | null $hintColor = null;

    protected string | Closure | null $hintIcon = null;

    public function hint(string | HtmlString | Closure | null $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function hintColor(string | Closure | null $hintColor): static
    {
        $this->hintColor = $hintColor;

        return $this;
    }

    public function hintIcon(string | Closure | null $hintIcon): static
    {
        $this->hintIcon = $hintIcon;

        return $this;
    }

    public function getHint(): string | HtmlString | null
    {
        return $this->evaluate($this->hint);
    }

    public function getHintColor(): ?string
    {
        return $this->evaluate($this->hintColor);
    }

    public function getHintIcon(): ?string
    {
        return $this->evaluate($this->hintIcon);
    }
}
