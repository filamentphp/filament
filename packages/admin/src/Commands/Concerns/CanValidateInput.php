<?php

namespace Filament\Commands\Concerns;

use Illuminate\Support\Facades\Validator;

trait CanValidateInput
{
    protected function validateInput(callable $callback, string $field, array $rules): string
    {
        $input = $callback();

        $validator = Validator::make(
            [$field => $input],
            [$field => $rules],
        );

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            $input = $this->validateInput($callback, $field, $rules);
        }

        return $input;
    }
}
