<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasHint
{
    protected string | Closure | null $hint = null;

    protected ?string $hintIcon = null;

    public function hint(string | Closure | null $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function getHint(): ?string
    {
        return $this->evaluate($this->hint);
    }

    public function hintIcon(string $hintIcon): static
    {
        $this->hintIcon = $hintIcon;

        return $this;
    }

    public function getHintIcon(): ?string
    {
        return $this->hintIcon;
    }
}
