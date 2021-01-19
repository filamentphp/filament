<?php

namespace Filament\Traits;

use Illuminate\Support\Facades\Validator;

trait ConsoleValidation
{
    protected function validateInput($callback, $field, $rules)
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
