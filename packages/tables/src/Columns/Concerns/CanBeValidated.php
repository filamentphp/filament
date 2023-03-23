<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait CanBeValidated
{
    protected array | Closure $rules = [];

    protected string | Closure | null $validationAttribute = null;

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

    public function getRules(): array
    {
        $rules = $this->evaluate($this->rules);

        if (! in_array('required', $rules)) {
            $rules[] = 'nullable';
        }

        return $rules;
    }

    public function validate($input): void
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
