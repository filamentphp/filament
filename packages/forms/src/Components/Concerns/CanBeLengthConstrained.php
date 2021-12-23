<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeLengthConstrained
{
    protected int | Closure | null $maxLength = null;

    protected int | Closure | null $minLength = null;

    public function maxLength(int | Closure $length): static
    {
        $this->maxLength = $length;

        $this->rule(function (): string {
            $length = $this->getMaxLength();

            return "max:{$length}";
        });

        return $this;
    }

    public function minLength(int | Closure $length): static
    {
        $this->minLength = $length;

        $this->rule(function (): string {
            $length = $this->getMinLength();

            return "min:{$length}";
        });

        return $this;
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
