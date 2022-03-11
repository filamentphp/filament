<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Support\Facades\Validator;

trait CanBeLengthConstrained
{
    protected int | Closure | null $maxLength = null;

    protected int | Closure | null $minLength = null;

    public function maxLength(int | Closure $length): static
    {
        Validator::extend('max_length', fn ($attribute, $value, $parameters) =>  strlen((string) $value) <= $parameters[0], __('validation.max.string'));
        Validator::replacer('max_length', fn ($message, $attribute, $rule, $parameters) =>  str_replace(':max', $parameters[0], $message));

        $this->maxLength = $length;

        $this->rule(function (): string {
            $length = $this->getMaxLength();

            return "max_length:{$length}";
        });

        return $this;
    }

    public function minLength(int | Closure $length): static
    {
        Validator::extend('min_length', fn ($attribute, $value, $parameters) =>  strlen((string) $value) >= $parameters[0], __('validation.min.string'));
        Validator::replacer('min_length', fn ($message, $attribute, $rule, $parameters) =>  str_replace(':min', $parameters[0], $message));

        $this->minLength = $length;

        $this->rule(function (): string {
            $length = $this->getMinLength();

            return "min_length:{$length}";
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
