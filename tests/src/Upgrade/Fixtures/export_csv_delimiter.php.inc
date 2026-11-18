<?php

namespace ExportDelimiter;

use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ImportAction;

function configure(ExportAction $action, ?ExportBulkAction $nullable, ImportAction $import, object $unrelated): void
{
    $action->csvDelimiter(';')->label('Export');
    ExportAction::make('export')->label('Export')->csvDelimiter(delimiter: ';');
    $action->csvDelimiter(makeDelimiter());
    $nullable?->csvDelimiter(makeDelimiter())->label('Export');
    $nullable?->csvDelimiter(';')->label('Export');
    $action->csvDelimiter(fn () => expensiveDelimiter());
    $import->csvDelimiter(';');
    $unrelated->csvDelimiter(';');
    $action->csvDelimiter(...$arguments);
    $callback = $action->csvDelimiter(...);
    $action->csvDelimiter($delimiter = makeDelimiter());
}
-----
<?php

namespace ExportDelimiter;

use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ImportAction;

function configure(ExportAction $action, ?ExportBulkAction $nullable, ImportAction $import, object $unrelated): void
{
    $action->label('Export');
    ExportAction::make('export')->label('Export');
    $action->tap(fn() => makeDelimiter());
    $nullable?->tap(fn() => makeDelimiter())->label('Export');
    $nullable?->tap(fn() => null)->label('Export');
    $action;
    $import->csvDelimiter(';');
    $unrelated->csvDelimiter(';');
    $action->csvDelimiter(...$arguments);
    $callback = $action->csvDelimiter(...);
    $action->csvDelimiter($delimiter = makeDelimiter());
}
