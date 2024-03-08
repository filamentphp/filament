<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeLengthConstrained
{
    protected int | Closure | null $length = null;

    protected int | Closure | null $maxLength = null;

    protected int | Closure | null $minLength = null;

    public function length(int | Closure | null $length): static
    {
        $this->length = $length;
        $this->maxLength = $length;
        $this->minLength = $length;

        return $this;
    }

    public function maxLength(int | Closure | null $length): static
    {
        $this->maxLength = $length;

        return $this;
    }

    public function minLength(int | Closure | null $length): static
    {
        $this->minLength = $length;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->evaluate($this->length);
    }

    public function getMaxLength(): ?int
    {
        return $this->evaluate($this->maxLength);
    }

    public function getMinLength(): ?int
    {
        return $this->evaluate($this->minLength);
    }
}
