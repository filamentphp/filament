<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeLengthConstrained
{
    protected $maxLength = null;

    protected $minLength = null;

    public function maxLength(int | callable $length): static
    {
        $this->maxLength = $length;

        $this->rule(function (): string {
            $length = $this->getMaxLength();

            return "max:{$length}";
        });

        return $this;
    }

    public function minLength(int | callable $length): static
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
