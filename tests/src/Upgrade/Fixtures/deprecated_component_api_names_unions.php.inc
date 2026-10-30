<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedComponentApiNamesUnions;

use Filament\Actions\Imports\ImportColumn;

class UnrelatedColumn
{
    public function guess(array $guesses): static
    {
        return $this;
    }
}

function configure(ImportColumn | UnrelatedColumn $unrelated, ImportColumn $base): void
{
    $unrelated->guess(guesses: ['name']);
    $base->guess(guesses: ['name']);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedComponentApiNamesUnions;

use Filament\Actions\Imports\ImportColumn;

class UnrelatedColumn
{
    public function guess(array $guesses): static
    {
        return $this;
    }
}

function configure(ImportColumn | UnrelatedColumn $unrelated, ImportColumn $base): void
{
    $unrelated->guess(guesses: ['name']);
    $base->guesses(guesses: ['name']);
}
