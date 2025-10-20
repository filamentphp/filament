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
     * @var array<string, string | Closure>
     */
    protected array $validationMessages = [];

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
     * @param  array<string, string | Closure>  $messages
     */
    public function validationMessages(array $messages): static
    {
        $this->validationMessages = $messages;

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
        $originalState = $this->getGetStateUsingCallback();

        $this->getStateUsing($input);

        try {
            Validator::make(
                ['input' => $input],
                ['input' => $this->getRules()],
                ['input' => $this->getValidationMessages()],
                ['input' => $this->getValidationAttribute()],
            )->validate();
        } finally {
            $this->getStateUsing($originalState);
        }
    }

    public function getValidationAttribute(): string
    {
        return $this->evaluate($this->validationAttribute) ?? Str::lcfirst($this->getLabel());
    }

    /**
     * @return array<string, string>
     */
    public function getValidationMessages(): array
    {
        $messages = [];

        foreach ($this->validationMessages as $rule => $message) {
            $messages[$rule] = $this->evaluate($message);
        }

        return array_filter($messages);
    }
}
