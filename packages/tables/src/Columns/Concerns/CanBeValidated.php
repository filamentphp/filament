<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait CanBeValidated
{
    /**
     * @var array<array-key> | Closure
     */
    protected array | Closure $rules = [];

    protected string | Closure | null $validationAttribute = null;

    /**
     * @param  array<array-key> | Closure  $rules
     */
    public function rules(array | Closure $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function validationAttribute(string | Closure | null $label): static
    {
        $this->validationAttribute = $label;

        return $this;
    }

    /**
     * @return array<array-key>
     */
    public function getRules(): array
    {
        $rules = $this->evaluate($this->rules);

        if (! in_array('required', $rules)) {
            $rules[] = 'nullable';
        }

        return $rules;
    }

    public function validate(mixed $input): void
    {
        Validator::make(
            ['input' => $input],
            ['input' => $this->getRules()],
            [],
            ['input' => $this->getValidationAttribute()],
        )->validate();
    }

    public function getValidationAttribute(): string
    {
        return $this->evaluate($this->validationAttribute) ?? Str::lcfirst($this->getLabel());
    }
}
