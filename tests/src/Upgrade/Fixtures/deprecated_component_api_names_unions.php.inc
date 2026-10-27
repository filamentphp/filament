<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedComponentApiNamesUnions;

use Closure;
use Filament\Actions\Imports\ImportColumn;

class CustomColumn extends ImportColumn
{
    public function guesses(array | Closure $guesses): static
    {
        return $this;
    }
}

class UnrelatedColumn
{
    public function guess(array $guesses): static
    {
        return $this;
    }
}

function configure(ImportColumn | CustomColumn $overridden, ImportColumn | UnrelatedColumn $unrelated, ImportColumn $base): void
{
    $overridden->guess(guesses: ['name']);
    $unrelated->guess(guesses: ['name']);
    $base->guess(guesses: ['name']);
    $overridden?->label('Name')?->guess(guesses: ['name']);
}
