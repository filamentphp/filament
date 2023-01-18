<?php

namespace Filament\Support\Commands\Concerns;

use Closure;
use Illuminate\Support\Facades\Validator;

trait CanValidateInput
{
    protected function askRequired(string $question, string $field, ?string $default = null): string
    {
        return $this->validateInput(fn () => $this->ask($question, $default), $field, ['required']);
    }

    protected function validateInput(Closure $callback, string $field, array $rules, ?Closure $onError = null): string
    {
        $input = $callback();

        $validator = Validator::make(
            [$field => $input],
            [$field => $rules],
        );

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            if ($onError) {
                $onError($validator);
            }

            $input = $this->validateInput($callback, $field, $rules);
        }

        return $input;
    }
}
