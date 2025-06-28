<?php

namespace Filament\Auth\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordStrength implements ValidationRule
{
    protected array $failedRules = [];
    protected int $min;
    protected bool $letters;
    protected bool $numbers;
    protected bool $symbols;
    protected bool $mixedCase;

    protected function __construct() { }

    public static function make(): static
    {
        $instance = new static();

        $instance->min = 8;
        $instance->letters = true;
        $instance->numbers = true;
        $instance->symbols = true;
        $instance->mixedCase = true;

        return $instance;
    }

    public function min(int $length): static
    {
        $this->min = $length;
        return $this;
    }

    public function letters(bool $enable = true): static
    {
        $this->letters = $enable;
        return $this;
    }

    public function numbers(bool $enable = true): static
    {
        $this->numbers = $enable;
        return $this;
    }

    public function symbols(bool $enable = true): static
    {
        $this->symbols = $enable;
        return $this;
    }

    public function mixedCase(bool $enable = true): static
    {
        $this->mixedCase = $enable;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $this->failedRules = [];

        if (!is_string($value)) {
            $fail(__('The :attribute must be a string.', ['attribute' => $attribute]));
            return;
        }

        if (strlen($value) < $this->min) {
            $this->failedRules[] = 'min_length';
        }

        if ($this->letters && !preg_match('/\pL/u', $value)) {
            $this->failedRules[] = 'letters';
        }

        if ($this->numbers && !preg_match('/\pN/u', $value)) {
            $this->failedRules[] = 'numbers';
        }

        if ($this->symbols && !preg_match('/\p{S}|\p{P}|\p{Z}/u', $value)) {
            $this->failedRules[] = 'symbols';
        }

        if ($this->mixedCase && !preg_match('/[a-z]/', $value) || !preg_match('/[A-Z]/', $value)) {
            $this->failedRules[] = 'mixed_case';
        }

        if (!empty($this->failedRules)) {
            $fail($this->constructMessage());
        }
    }

    protected function constructMessage(): string
    {
        $parts = [];

        foreach ($this->failedRules as $rule) {
            $parts[] = match ($rule) {
                'min_length' => __('at least :min characters', ['min' => $this->min]),
                'letters' => __('at least one letter'),
                'numbers' => __('at least one number'),
                'symbols' => __('at least one symbol'),
                'mixed_case' => __('at least one uppercase and one lowercase letter'),
                default => null,
            };
        }

        if (count($parts) === 1) {
            return __('The password must contain :requirement.', ['requirement' => $parts[0]]);
        }

        $last = array_pop($parts);
        return __('The password must contain :requirements, and :last.', [
            'requirements' => implode(', ', $parts),
            'last' => $last
        ]);
    }
}
