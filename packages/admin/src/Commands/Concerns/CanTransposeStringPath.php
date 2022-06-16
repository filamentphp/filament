<?php

namespace Filament\Commands\Concerns;

use Illuminate\Support\Str;

trait CanTransposeStringPath
{
    protected function changeForwardSlashToBackSlashes(string $value): string
    {
        return (string)Str::of($value)
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\')
            ->append('\\');
    }
}
