<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Contracts\CanHaveNumericState;

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

    /**
     * @return array<string>
     */
    public function getLengthValidationRules(): array
    {
        $isNumeric = $this instanceof CanHaveNumericState && $this->isNumeric();

        if (filled($length = $this->getLength())) {
            return $isNumeric ?
                ["digits:{$length}"] :
                ["size:{$length}"];
        }

        $rules = [];

        if (filled($maxLength = $this->getMaxLength())) {
            $rules[] = $isNumeric ?
                "max_digits:{$maxLength}" :
                "max:{$maxLength}";
        }

        if (filled($minLength = $this->getMinLength())) {
            $rules[] = $isNumeric ?
                "min_digits:{$minLength}" :
                "min:{$minLength}";
        }

        return $rules;
    }
}
