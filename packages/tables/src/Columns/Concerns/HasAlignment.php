<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait HasAlignment
{
    protected string | Closure | null $alignment = null;

    public function alignment(string | Closure | null $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function alignStart(bool | Closure $condition = true): static
    {
        return $this->alignment(static fn (): ?string => $condition ? 'start' : null);
    }

    public function alignCenter(bool | Closure $condition = true): static
    {
        return $this->alignment(static fn (): ?string => $condition ? 'center' : null);
    }

    public function alignEnd(bool | Closure $condition = true): static
    {
        return $this->alignment(static fn (): ?string => $condition ? 'end' : null);
    }

    public function alignJustify(bool | Closure $condition = true): static
    {
        return $this->alignment(static fn (): ?string => $condition ? 'justify' : null);
    }

    public function alignLeft(bool | Closure $condition = true): static
    {
        return $this->alignment(static fn (): ?string => $condition ? 'left' : null);
    }

    public function alignRight(bool | Closure $condition = true): static
    {
        return $this->alignment(static fn (): ?string => $condition ? 'right' : null);
    }

    public function getAlignment(): ?string
    {
        return $this->evaluate($this->alignment);
    }
}
