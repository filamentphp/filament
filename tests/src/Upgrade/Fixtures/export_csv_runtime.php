<?php

use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\ImportAction;

function check(bool $condition): void
{
    if (! $condition) {
        throw new RuntimeException('CSV migration changed behavior');
    }
}

function receiver(ExportAction | ExportBulkAction $action): ExportAction | ExportBulkAction
{
    $GLOBALS['events'][] = 'receiver';

    return $action;
}

function delimiter(): Closure
{
    $GLOBALS['events'][] = 'argument';

    return static function (): never {
        throw new RuntimeException('The stored delimiter must never be invoked');
    };
}

function label(): string
{
    $GLOBALS['events'][] = 'label';

    return 'Export';
}

function nullable(?ExportAction $action): void
{
    $GLOBALS['events'] = [];
    $value = null;
    check($action?->csvDelimiter($value = delimiter())->label(label()) === $action);
    check($GLOBALS['events'] === ($action ? ['argument', 'label'] : []));
    check(($value instanceof Closure) === ($action !== null));
}

foreach ([new ExportAction('export'), new ExportBulkAction('export')] as $action) {
    $GLOBALS['events'] = [];
    check(receiver($action)->label('Before')->csvDelimiter(delimiter: delimiter())->label(label()) === $action);
    check($GLOBALS['events'] === ['receiver', 'argument', 'label']);
    check($action->getLabel() === 'Export');
    check($action->csvDelimiter(';')->label('After')->csvDelimiter(null) === $action);
    check($action->csvDelimiter(static fn () => throw new RuntimeException('Never invoked')) === $action);
}
nullable(new ExportAction('export'));
nullable(null);
check(Exporter::getCsvDelimiter() === ',');
check((new ImportAction('import'))->csvDelimiter(';')->getCsvDelimiter() === ';');
echo "PASS\n";
