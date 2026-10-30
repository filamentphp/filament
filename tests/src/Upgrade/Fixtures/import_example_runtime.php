<?php

use Filament\Actions\Imports\ImportColumn;
use Filament\Schemas\Components\Section;

function check(bool $condition): void
{
    if (! $condition) {
        throw new RuntimeException('Getter migration changed behavior');
    }
}

function receiver(?ImportColumn $column): ?ImportColumn
{
    $GLOBALS['receivers']++;

    return $column;
}

$GLOBALS['receivers'] = 0;
$column = new ImportColumn('name');
check($column->examples(['associated' => 'first', 12 => 'second'])->getExample() === 'first');
check($column->examples([])->getExample() === null);
check($column->examples(['key' => null, 'later' => 'second'])->getExample() === null);
check($column->examples(fn (): array => ['key' => 'callback'])->getExample() === 'callback');
check(receiver($column)?->getExample() === 'callback');
check(receiver(null)?->getExample() === null);
check($GLOBALS['receivers'] === 2);
$component = Section::make();
check($component->isConcealed() === false);
check($component?->isConcealed() === false);
$component = null;
check($component?->isConcealed() === null);
echo "PASS\n";
