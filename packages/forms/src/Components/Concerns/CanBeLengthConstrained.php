<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Contracts;
use Filament\Forms\Components\Contracts\CanHaveNumericState;

trait CanBeLengthConstrained
{
    protected int | Closure | null $length = null;

    protected int | Closure | null $maxLength = null;

    protected int | Closure | null $minLength = null;

    public function length(int | Closure $length): static
    {
        $this->length = $length;
        $this->maxLength = $length;
        $this->minLength = $length;

        $this->rule(static function (Contracts\CanBeLengthConstrained $component): string {
            $length = $component->getLength();

            if ($component instanceof CanHaveNumericState && $component->isNumeric()) {
                return "digits:{$length}";
            }

            return "size:{$length}";
        });

        return $this;
    }

    public function maxLength(int | Closure $length): static
    {
        $this->maxLength = $length;

        $this->rule(static function (Contracts\CanBeLengthConstrained $component): string {
            $length = $component->getMaxLength();

            return "max:{$length}";
        });

        return $this;
    }

    public function minLength(int | Closure $length): static
    {
        $this->minLength = $length;

        $this->rule(static function (Contracts\CanBeLengthConstrained $component): string {
            $length = $component->getMinLength();

            return "min:{$length}";
        });

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
